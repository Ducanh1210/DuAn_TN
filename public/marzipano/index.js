/*
 * Copyright 2016 Google Inc. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
'use strict';

(function () {
  var Marzipano = window.Marzipano;
  var bowser = window.bowser;
  var screenfull = window.screenfull;
  var data = window.APP_DATA;

  // Grab elements from DOM.
  var panoElement = document.querySelector('#pano');
  var sceneNameElement = document.querySelector('#titleBar .sceneName');
  var sceneListElement = document.querySelector('#sceneList');
  var sceneElements = document.querySelectorAll('#sceneList .scene');
  var sceneListToggleElement = document.querySelector('#sceneListToggle');
  var autorotateToggleElement = document.querySelector('#autorotateToggle');
  var fullscreenToggleElement = document.querySelector('#fullscreenToggle');

  // Detect desktop or mobile mode.
  if (window.matchMedia) {
    var setMode = function () {
      if (mql.matches) {
        document.body.classList.remove('desktop');
        document.body.classList.add('mobile');
      } else {
        document.body.classList.remove('mobile');
        document.body.classList.add('desktop');
      }
    };
    var mql = matchMedia("(max-width: 500px), (max-height: 500px)");
    setMode();
    mql.addListener(setMode);
  } else {
    document.body.classList.add('desktop');
  }

  // Detect whether we are on a touch device.
  document.body.classList.add('no-touch');
  window.addEventListener('touchstart', function () {
    document.body.classList.remove('no-touch');
    document.body.classList.add('touch');
  });

  // Use tooltip fallback mode on IE < 11.
  if (bowser.msie && parseFloat(bowser.version) < 11) {
    document.body.classList.add('tooltip-fallback');
  }

  // Viewer options.
  var viewerOpts = {
    controls: {
      mouseViewMode: data.settings.mouseViewMode
    }
  };

  // Initialize viewer.
  var viewer = new Marzipano.Viewer(panoElement, viewerOpts);

  // Create scenes.
  var scenes = data.scenes.map(function (sceneData) {
    var source = Marzipano.ImageUrlSource.fromString(sceneData.url);
    var geometry = new Marzipano.EquirectGeometry([{ width: 4096 }]); // Dùng Equirectangular thay vì Cube

    var limiter = Marzipano.RectilinearView.limit.traditional(2048, 100 * Math.PI / 180, 120 * Math.PI / 180);
    var view = new Marzipano.RectilinearView(sceneData.initialViewParameters, limiter);

    var scene = viewer.createScene({
      source: source,
      geometry: geometry,
      view: view,
      pinFirstLevel: true
    });

    var hotspotItems = [];

    // Create link hotspots (hover-only).
    sceneData.linkHotspots.forEach(function (hotspot) {
      var element = createLinkHotspotElement(hotspot);
      scene.hotspotContainer().createHotspot(element, { yaw: hotspot.yaw, pitch: hotspot.pitch });
    });

    // Create info hotspots (auto-fade-in when camera faces them).
    sceneData.infoHotspots.forEach(function (hotspot) {
      var element = createInfoHotspotElement(hotspot);
      scene.hotspotContainer().createHotspot(element, { yaw: hotspot.yaw, pitch: hotspot.pitch });
      hotspotItems.push({ element: element, yaw: hotspot.yaw, pitch: hotspot.pitch });
    });

    // Automatically fade in hotspots when camera rotates to face them
    function updateHotspotsInView() {
      var curYaw = view.yaw();
      var curPitch = view.pitch();
      var fov = view.fov();

      // Widen viewing angle cone to cover nearly the full screen width (~100 degrees)
      var maxYaw = Math.max(fov * 0.65, 0.85);
      var maxPitch = 0.75;

      hotspotItems.forEach(function (item) {
        var yawDiff = Math.abs((item.yaw - curYaw + 3 * Math.PI) % (2 * Math.PI) - Math.PI);
        var pitchDiff = Math.abs(item.pitch - curPitch);

        if (yawDiff < maxYaw && pitchDiff < maxPitch) {
          item.element.classList.add('in-view');
        } else {
          item.element.classList.remove('in-view');
        }
      });
    }

    view.addEventListener('change', updateHotspotsInView);
    setTimeout(updateHotspotsInView, 150);

    return {
      data: sceneData,
      scene: scene,
      view: view
    };
  });

  // Set up autorotate, if enabled.
  var autorotate = Marzipano.autorotate({
    yawSpeed: 0.08,
    targetPitch: 0,
    targetFov: Math.PI / 2
  });
  if (data.settings.autorotateEnabled) {
    autorotateToggleElement.classList.add('enabled');
  }

  // Pause camera rotation when hovering over any hotspot
  document.addEventListener('mouseover', function(e) {
    if (e.target.closest('.hotspot')) {
      viewer.stopMovement();
    }
  });
  document.addEventListener('mouseout', function(e) {
    if (e.target.closest('.hotspot') && autorotateToggleElement.classList.contains('enabled')) {
      viewer.startMovement(autorotate);
    }
  });

  // Set handler for autorotate toggle.
  autorotateToggleElement.addEventListener('click', toggleAutorotate);

  // Set up fullscreen mode, if supported.
  if (screenfull.enabled && data.settings.fullscreenButton) {
    document.body.classList.add('fullscreen-enabled');
    fullscreenToggleElement.addEventListener('click', function () {
      screenfull.toggle();
    });
    screenfull.on('change', function () {
      if (screenfull.isFullscreen) {
        fullscreenToggleElement.classList.add('enabled');
      } else {
        fullscreenToggleElement.classList.remove('enabled');
      }
    });
  } else {
    document.body.classList.add('fullscreen-disabled');
  }

  // Set handler for scene list toggle.
  sceneListToggleElement.addEventListener('click', toggleSceneList);

  // Start with the scene list open on desktop.
  if (!document.body.classList.contains('mobile')) {
    showSceneList();
  }

  // Set handler for scene switch.
  scenes.forEach(function (scene) {
    var el = document.querySelector('#sceneList .scene[data-id="' + scene.data.id + '"]');
    if (el) {
      el.addEventListener('click', function () {
        switchScene(scene);
        // On mobile, hide scene list after selecting a scene.
        if (document.body.classList.contains('mobile')) {
          hideSceneList();
        }
      });
    }
  });

  // DOM elements for view controls.
  var viewUpElement = document.querySelector('#viewUp');
  var viewDownElement = document.querySelector('#viewDown');
  var viewLeftElement = document.querySelector('#viewLeft');
  var viewRightElement = document.querySelector('#viewRight');
  var viewInElement = document.querySelector('#viewIn');
  var viewOutElement = document.querySelector('#viewOut');

  // Dynamic parameters for controls.
  var velocity = 0.7;
  var friction = 3;

  // Associate view controls with elements.
  var controls = viewer.controls();
  controls.registerMethod('upElement', new Marzipano.ElementPressControlMethod(viewUpElement, 'y', -velocity, friction), true);
  controls.registerMethod('downElement', new Marzipano.ElementPressControlMethod(viewDownElement, 'y', velocity, friction), true);
  controls.registerMethod('leftElement', new Marzipano.ElementPressControlMethod(viewLeftElement, 'x', -velocity, friction), true);
  controls.registerMethod('rightElement', new Marzipano.ElementPressControlMethod(viewRightElement, 'x', velocity, friction), true);
  controls.registerMethod('inElement', new Marzipano.ElementPressControlMethod(viewInElement, 'zoom', -velocity, friction), true);
  controls.registerMethod('outElement', new Marzipano.ElementPressControlMethod(viewOutElement, 'zoom', velocity, friction), true);

  function sanitize(s) {
    return s.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
  }

  function switchScene(scene, customView) {
    window.currentSceneId = scene.data.id;
    stopAutorotate();
    
    if (customView) {
      scene.view.setParameters({ yaw: customView.yaw, pitch: customView.pitch });
    } else {
      scene.view.setParameters(scene.data.initialViewParameters);
    }
    
    scene.scene.switchTo();
    startAutorotate();
    updateSceneName(scene);
    updateSceneList(scene);
  }

  function updateSceneName(scene) {
    sceneNameElement.innerHTML = sanitize(scene.data.name);
  }

  function updateSceneList(scene) {
    for (var i = 0; i < sceneElements.length; i++) {
      var el = sceneElements[i];
      if (el.getAttribute('data-id') === scene.data.id) {
        el.classList.add('current');
      } else {
        el.classList.remove('current');
      }
    }
  }

  function showSceneList() {
    sceneListElement.classList.add('enabled');
    sceneListToggleElement.classList.add('enabled');
  }

  function hideSceneList() {
    sceneListElement.classList.remove('enabled');
    sceneListToggleElement.classList.remove('enabled');
  }

  function toggleSceneList() {
    sceneListElement.classList.toggle('enabled');
    sceneListToggleElement.classList.toggle('enabled');
  }

  function startAutorotate() {
    if (!autorotateToggleElement.classList.contains('enabled')) {
      return;
    }
    viewer.startMovement(autorotate);
    viewer.setIdleMovement(3000, autorotate);
  }

  function stopAutorotate() {
    viewer.stopMovement();
    viewer.setIdleMovement(Infinity);
  }

  function toggleAutorotate() {
    if (autorotateToggleElement.classList.contains('enabled')) {
      autorotateToggleElement.classList.remove('enabled');
      stopAutorotate();
    } else {
      autorotateToggleElement.classList.add('enabled');
      startAutorotate();
    }
  }

  function createLinkHotspotElement(hotspot) {

    // Create wrapper element to hold icon and tooltip.
    var wrapper = document.createElement('div');
    wrapper.classList.add('hotspot');
    wrapper.classList.add('link-hotspot');
    wrapper.setAttribute('data-id', hotspot.id);
    wrapper.style.setProperty('--base-scale', hotspot.scale || 1.0);



    // Create dot element.
    var icon = document.createElement('div');
    icon.classList.add('link-hotspot-icon');
    icon.classList.add('pulsing-dot');

    // Set rotation transform.
    var transformProperties = ['-ms-transform', '-webkit-transform', 'transform'];
    for (var i = 0; i < transformProperties.length; i++) {
      var property = transformProperties[i];
      icon.style[property] = 'rotate(' + hotspot.rotation + 'rad)';
    }

    // Add click event handler.
    wrapper.addEventListener('click', function (e) {
      if (window.isEditorMode) {
        document.querySelectorAll('.hotspot').forEach(function(h) { 
            h.classList.remove('active-menu'); 
            h.style.zIndex = '';
        });
        wrapper.classList.add('active-menu');
        wrapper.style.zIndex = '100000';
        return; // Handled by context menu buttons
      }
      var targetScene = findSceneById(hotspot.target);
      if (targetScene) {
        if (hotspot.target_yaw !== null && hotspot.target_yaw !== undefined) {
          switchScene(targetScene, { yaw: hotspot.target_yaw, pitch: hotspot.target_pitch });
        } else {
          switchScene(targetScene);
        }
      }
    });

    // Prevent touch and scroll events from reaching the parent element.
    stopTouchAndScrollEventPropagation(wrapper);

    // Create SVG Dashed Connector + Badge Card
    var tooltip = document.createElement('div');
    tooltip.classList.add('hotspot-tooltip');
    tooltip.classList.add('link-hotspot-tooltip');

    var targetData = findSceneDataById(hotspot.target);
    var sceneTitle = targetData ? targetData.name : 'Chưa liên kết';

    tooltip.innerHTML = `
      <svg class="hotspot-dashed-connector" viewBox="0 0 30 30">
        <line x1="0" y1="30" x2="30" y2="0" class="dashed-line-path"></line>
      </svg>
      <div class="hotspot-badge-card">
        <div class="hotspot-badge-icon">
          <svg class="hotspot-exit-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
            <path d="M13.34,8.17C12.41,8.17 11.65,7.4 11.65,6.47A1.69,1.69 0 0,1 13.34,4.78C14.28,4.78 15.04,5.54 15.04,6.47C15.04,7.4 14.28,8.17 13.34,8.17M10.3,19.93L4.37,18.75L4.71,17.05L8.86,17.9L10.21,11.04L8.69,11.64V14.5H7V10.54L11.4,8.67L12.07,8.59C12.67,8.59 13.17,8.93 13.5,9.44L14.36,10.79C15.04,12 16.39,12.82 18,12.82V14.5C16.14,14.5 14.44,13.67 13.34,12.4L12.84,14.94L14.61,16.63V23H12.92V17.9L11.14,16.21L10.3,19.93M21,23H19V3H6V16.11L4,15.69V1H21V23M6,23H4V19.78L6,20.2V23Z"/>
          </svg>
        </div>
        <div class="hotspot-badge-title">${sceneTitle}</div>
      </div>
    `;

    wrapper.appendChild(icon);
    wrapper.appendChild(tooltip);

    // In editor mode, append context menu
    if (window.isEditorMode) {
      var menu = document.createElement('div');
      menu.className = 'hotspot-context-menu';
      menu.innerHTML = `
        <div class="context-menu-btn btn-go" title="Đi đến" onclick="event.stopPropagation(); window.onHotspotAction('${hotspot.id}', 'link', 'go')"><i class="fas fa-sign-in-alt"></i></div>
        <div class="context-menu-btn btn-rotate" title="Đổi hướng" onclick="event.stopPropagation(); window.onHotspotAction('${hotspot.id}', 'link', 'rotate')"><i class="fas fa-sync-alt"></i></div>
        <div class="context-menu-btn btn-delete" title="Xóa" onclick="event.stopPropagation(); window.onHotspotAction('${hotspot.id}', 'link', 'delete')"><i class="fas fa-trash"></i></div>
        <div class="context-menu-btn btn-edit" title="Chỉnh sửa" onclick="event.stopPropagation(); window.onHotspotAction('${hotspot.id}', 'link', 'edit')"><i class="fas fa-pencil-alt"></i></div>
      `;
      wrapper.appendChild(menu);
    }

    return wrapper;
  }

  function createInfoHotspotElement(hotspot) {

    // Create wrapper element to hold icon and tooltip.
    var wrapper = document.createElement('div');
    wrapper.classList.add('hotspot');
    wrapper.classList.add('info-hotspot');
    wrapper.setAttribute('data-id', hotspot.id);
    wrapper.style.setProperty('--base-scale', hotspot.scale || 1.0);



    // Create micro anchor dot
    var anchor = document.createElement('div');
    anchor.classList.add('info-hotspot-anchor');

    // Prevent touch and scroll events
    stopTouchAndScrollEventPropagation(wrapper);

    // Create SVG Dashed Connector + Badge Card for Info Hotspot
    var tooltip = document.createElement('div');
    tooltip.classList.add('hotspot-tooltip');
    tooltip.classList.add('info-hotspot-tooltip');

    tooltip.innerHTML = `
      <svg class="hotspot-dashed-connector" viewBox="0 0 30 30">
        <line x1="0" y1="30" x2="30" y2="0" class="dashed-line-path"></line>
      </svg>
      <div class="hotspot-badge-card">
        <div class="hotspot-badge-icon info-badge-icon">
          <i class="fa-solid fa-circle-info"></i>
        </div>
        <div class="hotspot-badge-title info-badge-title">${hotspot.title || ''}</div>
      </div>
    `;

    // Click handler to open modal/editor
    wrapper.addEventListener('click', function (e) {
      if (window.isEditorMode) {
        document.querySelectorAll('.hotspot').forEach(function(h) { 
            h.classList.remove('active-menu'); 
            h.style.zIndex = '';
        });
        wrapper.classList.add('active-menu');
        wrapper.style.zIndex = '100000';
        return;
      }
      showInfoModal(hotspot);
    });

    wrapper.appendChild(anchor);
    wrapper.appendChild(tooltip);

    // In editor mode, append context menu
    if (window.isEditorMode) {
      var menu = document.createElement('div');
      menu.className = 'hotspot-context-menu';
      menu.innerHTML = `
        <div class="context-menu-btn btn-delete" title="Xóa" onclick="event.stopPropagation(); window.onHotspotAction('${hotspot.id}', 'info', 'delete')"><i class="fas fa-trash"></i></div>
        <div class="context-menu-btn btn-edit" title="Chỉnh sửa" onclick="event.stopPropagation(); window.onHotspotAction('${hotspot.id}', 'info', 'edit')"><i class="fas fa-pencil-alt"></i></div>
      `;
      wrapper.appendChild(menu);
    }

    return wrapper;
  }

  // Prevent touch and scroll events from reaching the parent element.
  function stopTouchAndScrollEventPropagation(element, eventList) {
    var eventList = ['touchstart', 'touchmove', 'touchend', 'touchcancel',
      'wheel', 'mousewheel'];
    // In editor mode, also block mousedown so Marzipano's internal drag
    // handler doesn't fight with the custom hotspot drag logic.
    if (window.isEditorMode) {
      eventList.push('mousedown');
    }
    for (var i = 0; i < eventList.length; i++) {
      element.addEventListener(eventList[i], function (event) {
        event.stopPropagation();
      });
    }
  }

  function findSceneById(id) {
    for (var i = 0; i < scenes.length; i++) {
      if (scenes[i].data.id === id) {
        return scenes[i];
      }
    }
    return null;
  }

  function findSceneDataById(id) {
    for (var i = 0; i < data.scenes.length; i++) {
      if (data.scenes[i].id === id) {
        return data.scenes[i];
      }
    }
    return null;
  }

  // Display the initial scene.
  if (scenes.length > 0) {
    switchScene(scenes[0]);
  }

  // EXPORT TO WINDOW FOR EDITOR
  window.mzViewer = viewer;
  window.mzScenes = scenes;
  window.switchScene = switchScene;

  window.addHotspotToActiveScene = function (hotspot) {
    var sceneObj = findSceneById(window.currentSceneId);
    if (!sceneObj) return;

    var element;
    var yawRad = hotspot.yaw * Math.PI / 180;
    var pitchRad = hotspot.pitch * Math.PI / 180;
    var type = hotspot.hotspot_type || hotspot.type;
    var target = hotspot.target_panorama_id || hotspot.target;
    var title = hotspot.title || '';
    var text = hotspot.content || '';

    if (type === 'info') {
      element = createInfoHotspotElement({
        id: hotspot.id,
        title: title,
        text: text
      });
      var sceneData = findSceneDataById(window.currentSceneId);
      if (sceneData) {
        sceneData.infoHotspots = sceneData.infoHotspots || [];
        sceneData.infoHotspots.push({
          id: hotspot.id,
          title: title,
          text: text,
          yaw: yawRad,
          pitch: pitchRad
        });
      }
    } else {
      element = createLinkHotspotElement({
        id: hotspot.id,
        target: target,
        rotation: 0
      });
      var sceneData = findSceneDataById(window.currentSceneId);
      if (sceneData) {
        sceneData.linkHotspots = sceneData.linkHotspots || [];
        sceneData.linkHotspots.push({
          id: hotspot.id,
          target: target,
          rotation: 0,
          yaw: yawRad,
          pitch: pitchRad
        });
      }
    }

    sceneObj.scene.hotspotContainer().createHotspot(element, { yaw: yawRad, pitch: pitchRad });
  };
})();
