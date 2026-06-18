
import os
import time
import asyncio
import numpy as np
from fastapi import FastAPI
from fastapi.responses import HTMLResponse, StreamingResponse
import uvicorn
from vieneu import Vieneu
import io
import wave
from huggingface_hub import hf_hub_download

# ==========================================
# CONFIG GGUF MODELS
# ==========================================
AVAILABLE_MODELS = {
    "v3turbo": {
        "id": "pnnbao-ump/VieNeu-TTS-v3-Turbo",
        "name": "VieNeu v3 Turbo (ONNX/CPU)",
        "desc": "🆕 v3 Turbo (48kHz) - Fast, CPU optimized, contains 20+ built-in voices."
    },
    "q4": {
        "id": "pnnbao-ump/VieNeu-TTS-0.3B-q4-gguf",
        "name": "VieNeu 0.3B (Q4_0) - Fast/Light",
        "desc": "Recommended for most CPUs (Speed > Quality)"
    },
    "q8": {
        "id": "pnnbao-ump/VieNeu-TTS-0.3B-q8-gguf",
        "name": "VieNeu 0.3B (Q8_0) - High Quality",
        "desc": "Higher quality but slower (Requires strong CPU)"
    },
    "ngochuyen": {
        "id": "pnnbao-ump/VieNeu-TTS-0.3B-ngoc-huyen-gguf-Q4_0",
        "name": "VieNeu 0.3B (Q4_0) - Ngoc Huyen",
        "desc": "Ngoc Huyen Voice"
    }
}

DEFAULT_MODEL = "v3turbo"
current_model_id = DEFAULT_MODEL
app = FastAPI()

# Global TTS Instance
tts = None

def load_model_instance(model_key):
    global tts, current_model_id
    print(f"⏳ Loading Model: {model_key}...")
    
    repo_id = ""
    
    # Check if this is a preset model key
    if model_key in AVAILABLE_MODELS:
        repo_id = AVAILABLE_MODELS[model_key]["id"]
    else:
        # Assume it's a custom Hugging Face Repo ID
        # Validation: Must contain 'gguf' or 'v3-turbo' (case-insensitive)
        if "gguf" not in model_key.lower() and "v3-turbo" not in model_key.lower():
            raise ValueError("Custom Model ID must contain 'gguf' or 'v3-turbo'")
        
        repo_id = model_key.strip()
        print(f"🔄 Custom Model Detected: {repo_id}")

    # Reload TTS
    try:
        if "v3-turbo" in repo_id.lower() or model_key == "v3turbo":
            new_tts = Vieneu(
                mode='v3turbo', 
                backbone_repo=repo_id,
                device="cpu"
            )
        else:
            new_tts = Vieneu(
                mode='standard', 
                backbone_repo=repo_id,
                backbone_device="cpu", 
                codec_repo="neuphonic/neucodec-onnx-decoder-int8", 
                codec_device="cpu",
                gguf_filename=None
            )
        tts = new_tts
        current_model_id = model_key
        print(f"✅ Model Loaded Successfully: {repo_id}")
    except Exception as e:
        print(f"❌ Failed to load model {repo_id}: {e}")
        raise e

# Initial Load
try:
    load_model_instance(DEFAULT_MODEL)
except Exception:
    print("⚠️ Initial model load failed. Server running but needs valid model.")


# ==========================================
# UI SERVING
# ==========================================
try:
    with open(os.path.join(os.path.dirname(os.path.dirname(__file__)), "client", "client.html"), "r", encoding="utf-8") as f:
        HTML_CONTENT = f.read()
    HTML_CONTENT = HTML_CONTENT.replace("VieNeu Stream", "VieNeu GGUF (CPU)")
    HTML_CONTENT = HTML_CONTENT.replace("Server: LMDeploy (Remote)", "Server: Local GGUF (CPU)")
except FileNotFoundError:
    HTML_CONTENT = "<h1>Error: client.html missing</h1>"

@app.get("/")
async def get_ui():
    return HTMLResponse(content=HTML_CONTENT)

@app.get("/models")
async def get_models():
    """Return available models"""
    return [
        {"key": k, "name": v["name"], "desc": v["desc"], "active": k == current_model_id}
        for k, v in AVAILABLE_MODELS.items()
    ]

from typing import Optional
from pydantic import BaseModel, Field
from vieneu_utils.url_extract import extract_text_from_url

class ModelRequest(BaseModel):
    model_key: str

class UrlRequest(BaseModel):
    url: str
    max_chars: int = Field(default=5000, le=20000)

class StreamRequest(BaseModel):
    text: str
    voice_id: Optional[str] = None
    emotion: Optional[str] = "natural"
    temperature: Optional[float] = 0.8
    top_k: Optional[int] = 25
    top_p: Optional[float] = 0.95
    repetition_penalty: Optional[float] = 1.2

@app.post("/set_model")
async def set_model(req: ModelRequest):
    """Switch Model"""
    try:
        load_model_instance(req.model_key)
        return {"status": "ok", "current_model": req.model_key}
    except Exception as e:
        return {"status": "error", "message": str(e)}

@app.post("/extract_url")
async def extract_url(req: UrlRequest):
    """Extract article text from a URL."""
    result = extract_text_from_url(req.url, max_chars=req.max_chars)
    if result["error"]:
        return {"status": "error", "message": result["error"]}
    return {
        "status": "ok",
        "title": result["title"],
        "text": result["text"],
        "char_count": result["char_count"],
        "truncated": result["truncated"],
    }

@app.get("/voices")
async def get_voices():
    """Return list of available voices. If none/error, return instruction."""
    try:
        if tts is None:
             return [{"id": "error", "name": "Model not loaded yet"}]

        voices = tts.list_preset_voices()
        
        if not voices:
             # Voices.json missing or empty
             return [{"id": "error_no_voices", "name": "⚠️ ERROR: No voices found! Please create voices.json in the model folder."}]

        # Normalize to list of objects for easier JS handling
        result = []
        if isinstance(voices[0], tuple):
            for desc, vid in voices:
                result.append({"id": vid, "name": desc})
        else:
            # Fallback if list is just strings
            for vid in voices:
                result.append({"id": vid, "name": vid})
        return result
    except Exception as e:
        print(f"Error listing voices: {e}")
        return [{"id": "error_exception", "name": f"⚠️ Error loading voices: {str(e)}"}]

def float32_to_pcm16(audio_float):
    """Convert float32 [-1, 1] to int16 bytes"""
    audio_int16 = (audio_float * 32767).clip(-32768, 32767).astype(np.int16)
    return audio_int16.tobytes()

@app.get("/stream")
async def stream_audio(
    text: str, 
    voice_id: str = None,
    emotion: str = "natural",
    temperature: float = 0.8,
    top_k: int = 25,
    top_p: float = 0.95,
    repetition_penalty: float = 1.2
):
    """Streaming Endpoint with Voice and Advanced Settings Support"""
    
    voice_data = None
    if voice_id:
        try:
            voice_data = tts.get_preset_voice(voice_id)
        except Exception:
            print(f"Voice {voice_id} not found, using default.")

    def audio_generator():
        header = io.BytesIO()
        with wave.open(header, 'wb') as wav_file:
            wav_file.setnchannels(1)
            wav_file.setsampwidth(2)
            wav_file.setframerate(getattr(tts, 'sample_rate', 24000))
            wav_file.setnframes(100_000_000) 
        yield header.getvalue()
        
        start = time.time()
        count = 0
        try:
            for chunk in tts.infer_stream(
                text, 
                voice=voice_data,
                emotion=emotion,
                temperature=temperature,
                top_k=top_k,
                top_p=top_p,
                repetition_penalty=repetition_penalty
            ):
                if count == 0:
                     print(f"⚡ First sound in {time.time() - start:.3f}s")
                count += 1
                yield float32_to_pcm16(chunk)
                time.sleep(0.001) 
                
        except Exception as e:
            print(f"Error during inference: {e}")

    return StreamingResponse(audio_generator(), media_type="audio/wav")

@app.post("/stream")
async def stream_audio_post(req: StreamRequest):
    """Streaming Endpoint via POST (for long text from URL extraction)."""
    return await stream_audio(
        req.text, 
        req.voice_id,
        emotion=req.emotion,
        temperature=req.temperature,
        top_k=req.top_k,
        top_p=req.top_p,
        repetition_penalty=req.repetition_penalty
    )

def main():
    print("🌍 Open http://localhost:8001 to test GGUF Streaming")
    uvicorn.run(app, host="127.0.0.1", port=8001)

if __name__ == "__main__":
    main()
