import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

/**
 * Photorealistic 3D Indian AI Presenter Engine for MotoShowroom
 * - Authentic Indian Female (Priya - Namaste, Saree, Bindi) & Indian Male (Rohit - TVS Advisor)
 * - Full Head Framing with graceful headroom (no cropping of forehead/hair)
 * - Syllable-level phoneme lip-sync matching spoken words & Web Speech API boundaries
 * - Natural breathing, randomized eyelid blinking, and cursor gaze tracking
 */
export class Avatar3DViewer {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.querySelector(container) : container;
        if (!this.container) {
            console.warn('[Avatar3DViewer] Target container not found:', container);
            return;
        }

        this.options = Object.assign({
            modelUrl: '/models/avatars/advisor-female.glb',
            interactive: true,
            cameraFov: 28,
            onLoad: null,
            onError: null,
        }, options);

        this.disposed = false;
        this.isLoading = false;
        this.model = null;
        this.morphMeshes = [];
        this.bones = {};
        this.restRotations = {};
        this.bindiMesh = null;

        // Morph target states for smooth lerping
        this.currentMorphs = {};
        this.targetMorphs = {};

        // Speech & Lip-Sync state
        this.isSpeaking = false;
        this.speechAmplitude = 0;
        this.speechSource = 'audio';
        this.currentWordBurst = null;

        // Blinking system
        this.blinkState = 0;
        this.nextBlinkTime = performance.now() + 2500 + Math.random() * 2000;
        this.blinkDuration = 180;
        this.blinkStartTime = 0;

        // Pointer / gaze tracking
        this.targetLook = { x: 0, y: 0 };
        this.currentLook = { x: 0, y: 0 };

        this.clock = new THREE.Clock();
        this.initScene();
        this.setupEventListeners();

        if (this.options.modelUrl) {
            this.loadModel(this.options.modelUrl);
        }

        this.animate = this.animate.bind(this);
        this.animFrameId = requestAnimationFrame(this.animate);
    }

    initScene() {
        const width = this.container.clientWidth || 300;
        const height = this.container.clientHeight || 360;

        this.scene = new THREE.Scene();

        // Camera: 28° FOV (portrait focal length for natural human proportions)
        this.camera = new THREE.PerspectiveCamera(this.options.cameraFov, width / height, 0.1, 50);
        this.camera.position.set(0, 1.45, 1.2);

        // Renderer with transparent background and ACES Filmic tone mapping
        this.renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance',
        });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.18;
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;
        this.renderer.domElement.style.width = '100%';
        this.renderer.domElement.style.height = '100%';
        this.renderer.domElement.style.display = 'block';
        this.renderer.domElement.style.pointerEvents = 'none';

        if (getComputedStyle(this.container).position === 'static') {
            this.container.style.position = 'relative';
        }
        this.container.innerHTML = '';
        this.container.appendChild(this.renderer.domElement);

        // Studio Lighting tailored for warm Indian skin tones
        const ambientLight = new THREE.AmbientLight(0xfff5ea, 1.4);
        this.scene.add(ambientLight);

        // Key light (soft warm sun/studio flash)
        const keyLight = new THREE.DirectionalLight(0xffeedd, 2.3);
        keyLight.position.set(1.4, 2.4, 2.0);
        this.scene.add(keyLight);

        // Fill light (diffuse cool tone from left)
        const fillLight = new THREE.DirectionalLight(0xdde8fa, 1.1);
        fillLight.position.set(-1.8, 1.6, 1.6);
        this.scene.add(fillLight);

        // Rim / Back light (gives beautiful rim light on dark Indian hair and shoulders)
        const rimLight = new THREE.DirectionalLight(0xffdfc2, 1.8);
        rimLight.position.set(0.0, 2.2, -2.2);
        this.scene.add(rimLight);

        this.resizeObserver = new ResizeObserver(() => this.handleResize());
        this.resizeObserver.observe(this.container);
    }

    setupEventListeners() {
        if (!this.options.interactive) return;

        this.onPointerMove = (e) => {
            const rect = this.container.getBoundingClientRect();
            if (rect.width === 0 || rect.height === 0) return;

            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            const x = ((clientX - rect.left) / rect.width) * 2 - 1;
            const y = -(((clientY - rect.top) / rect.height) * 2 - 1);

            this.targetLook.x = Math.max(-0.5, Math.min(0.5, x));
            this.targetLook.y = Math.max(-0.35, Math.min(0.35, y));
        };

        this.onPointerLeave = () => {
            this.targetLook.x = 0;
            this.targetLook.y = 0;
        };

        window.addEventListener('mousemove', this.onPointerMove, { passive: true });
        window.addEventListener('touchmove', this.onPointerMove, { passive: true });
        document.addEventListener('mouseleave', this.onPointerLeave, { passive: true });
    }

    handleResize() {
        if (this.disposed || !this.renderer || !this.camera || !this.container) return;
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        if (width === 0 || height === 0) return;

        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
        this.frameCameraOnFace();
    }

    loadModel(url) {
        if (this.disposed) return;
        this.isLoading = true;

        this.isFemale = url.toLowerCase().includes('female');

        const loader = new GLTFLoader();
        loader.load(
            url,
            (gltf) => {
                if (this.disposed) return;

                if (this.model) {
                    this.scene.remove(this.model);
                    this.disposeHierarchy(this.model);
                    this.model = null;
                }

                this.model = gltf.scene;
                this.morphMeshes = [];
                this.bones = {};
                this.restRotations = {};
                this.bindiMesh = null;

                // Discover bones and customize materials for authentic Indian appearance
                this.model.traverse((child) => {
                    if (child.isMesh) {
                        child.frustumCulled = false;
                        const name = child.name;

                        // REMOVE GLASSES on female
                        if (name === 'Wolf3D_Glasses') {
                            child.visible = false;
                        }

                        // Customize skin, hair, and clothing for Indian advisor
                        if (child.material) {
                            child.material.side = THREE.FrontSide;

                            if (this.isFemale) {
                                // Indian Female Advisor (Priya)
                                if (name.includes('Head') || name.includes('Skin') || name.includes('Body')) {
                                    // Subtle warm golden wheatish Indian tone
                                    child.material.color = new THREE.Color(1.0, 0.94, 0.88);
                                    child.material.roughness = 0.65;
                                } else if (name.includes('Hair')) {
                                    // Deep rich glossy black hair
                                    child.material.color = new THREE.Color(0.18, 0.14, 0.12);
                                    child.material.roughness = 0.35;
                                } else if (name.includes('Outfit_Top') || name.includes('Outfit')) {
                                    // Elegant Indian Saree/Kurti Royal Crimson Red
                                    child.material.color = new THREE.Color(0.72, 0.12, 0.16);
                                    child.material.roughness = 0.55;
                                }
                            } else {
                                // Indian Male TVS Advisor (Rohit)
                                if (name.includes('Head') || name.includes('Body') || name.includes('Skin')) {
                                    // Natural photorealistic male advisor tone
                                    child.material.color = new THREE.Color(0.98, 0.96, 0.94);
                                    child.material.roughness = 0.68;
                                } else if (name.includes('hair') || name.includes('Hair')) {
                                    // Black hair
                                    child.material.color = new THREE.Color(0.15, 0.12, 0.10);
                                    child.material.roughness = 0.4;
                                } else if (name.includes('look') || name.includes('outfit') || name.includes('shirt')) {
                                    // TVS Advisor Official Navy Blue Polo
                                    child.material.color = new THREE.Color(0.12, 0.22, 0.54);
                                }
                            }
                            child.material.needsUpdate = true;
                        }

                        if (child.morphTargetDictionary && child.morphTargetInfluences) {
                            this.morphMeshes.push(child);
                        }
                    }

                    if (child.isBone || (child.type && child.type.includes('Bone'))) {
                        const name = child.name;
                        this.bones[name] = child;
                        this.restRotations[name] = {
                            x: child.rotation.x,
                            y: child.rotation.y,
                            z: child.rotation.z,
                            quaternion: child.quaternion.clone(),
                        };
                    }
                });

                // Attach traditional Indian Red Bindi on Priya's forehead
                if (this.isFemale) {
                    const headBone = this.bones['Head'];
                    if (headBone) {
                        const bindiGeo = new THREE.CircleGeometry(0.0055, 32);
                        const bindiMat = new THREE.MeshBasicMaterial({
                            color: 0x991b1b,
                            side: THREE.DoubleSide,
                            depthTest: false,
                        });
                        this.bindiMesh = new THREE.Mesh(bindiGeo, bindiMat);
                        this.bindiMesh.position.set(0, 0.104, 0.077);
                        headBone.add(this.bindiMesh);
                    }
                }

                // Pose arms into authentic Namaste greeting (hands folded in front of chest)
                this.applyNamastePose(0);

                // Perfectly frame the full head, hair, neck, shoulders, and folded Namaste hands
                this.frameCameraOnFace();

                // Warm welcoming advisor smile
                this.targetMorphs['mouthSmile'] = 0.14;

                this.scene.add(this.model);
                this.isLoading = false;

                if (typeof this.options.onLoad === 'function') {
                    this.options.onLoad(this);
                }
            },
            undefined,
            (err) => {
                console.error('[Avatar3DViewer] Error loading avatar GLB:', err);
                this.isLoading = false;
                if (typeof this.options.onError === 'function') {
                    this.options.onError(err);
                }
            }
        );
    }

    /**
     * Fold arms into the traditional Indian Namaste greeting gesture (Anjali Mudra)
     */
    applyNamastePose(breathCycle = 0) {
        if (!this.bones || !this.restRotations) return;

        const lArm = this.bones['LeftArm'];
        const lForeArm = this.bones['LeftForeArm'];
        const lHand = this.bones['LeftHand'];

        const rArm = this.bones['RightArm'];
        const rForeArm = this.bones['RightForeArm'];
        const rHand = this.bones['RightHand'];

        const breathOffset = breathCycle * 0.006;

        if (this.isFemale) {
            // Priya (A-pose ReadyPlayerMe rig): Fold hands together in front of sternum
            if (lArm && this.restRotations['LeftArm']) {
                lArm.quaternion.copy(this.restRotations['LeftArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(0.40 + breathOffset, 0.20, 0.20)));
            }
            if (lForeArm && this.restRotations['LeftForeArm']) {
                lForeArm.quaternion.copy(this.restRotations['LeftForeArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(0, 0.40, 1.40)));
            }
            if (lHand && this.restRotations['LeftHand']) {
                lHand.quaternion.copy(this.restRotations['LeftHand'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(-0.10, -0.30, 1.10)));
            }

            if (rArm && this.restRotations['RightArm']) {
                rArm.quaternion.copy(this.restRotations['RightArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(0.40 + breathOffset, -0.20, -0.20)));
            }
            if (rForeArm && this.restRotations['RightForeArm']) {
                rForeArm.quaternion.copy(this.restRotations['RightForeArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(0, -0.40, -1.40)));
            }
            if (rHand && this.restRotations['RightHand']) {
                rHand.quaternion.copy(this.restRotations['RightHand'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(-0.10, 0.30, -1.10)));
            }
        } else {
            // Rohit TVS (T-pose rig): Fold hands together in front of sternum
            if (lArm && this.restRotations['LeftArm']) {
                lArm.quaternion.copy(this.restRotations['LeftArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(1.30 + breathOffset, 0, 0.50)));
            }
            if (lForeArm && this.restRotations['LeftForeArm']) {
                lForeArm.quaternion.copy(this.restRotations['LeftForeArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(0, 0.90, 1.60)));
            }
            if (lHand && this.restRotations['LeftHand']) {
                lHand.quaternion.copy(this.restRotations['LeftHand'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(-0.70, -0.50, 0.90)));
            }

            if (rArm && this.restRotations['RightArm']) {
                rArm.quaternion.copy(this.restRotations['RightArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(1.30 + breathOffset, 0, -0.50)));
            }
            if (rForeArm && this.restRotations['RightForeArm']) {
                rForeArm.quaternion.copy(this.restRotations['RightForeArm'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(0, -0.90, -1.60)));
            }
            if (rHand && this.restRotations['RightHand']) {
                rHand.quaternion.copy(this.restRotations['RightHand'].quaternion)
                    .multiply(new THREE.Quaternion().setFromEuler(new THREE.Euler(-0.70, 0.50, -0.90)));
            }
        }
    }

    /**
     * Accurately frames the FULL HEAD, hair, shoulders, and folded Namaste hands
     */
    frameCameraOnFace() {
        if (!this.model) return;

        const box = new THREE.Box3().setFromObject(this.model);
        const center = box.getCenter(new THREE.Vector3());

        // Top of hair
        const topOfHead = box.max.y;

        // Generous vertical span framing from above the hair down through the chest and folded hands
        const frameHeight = this.isFemale ? 0.68 : 0.74;

        // Center on upper chest / neck so top of head has ~4-5cm headroom and hands are visible
        const targetY = topOfHead - frameHeight * 0.46;
        const targetX = center.x;
        const targetZ = center.z;

        // Calculate distance so frameHeight fits comfortably within camera vertical FOV
        const fovRad = (this.camera.fov * Math.PI) / 180;
        const requiredDist = (frameHeight / 2) / Math.tan(fovRad / 2);

        this.camera.position.set(targetX, targetY, targetZ + requiredDist);
        this.camera.lookAt(targetX, targetY, targetZ);
        this.cameraTarget = new THREE.Vector3(targetX, targetY, targetZ);
    }

    /**
     * Syllable burst triggered on Web Speech API word boundary
     */
    pulseWord(word) {
        const w = (word || '').toLowerCase().replace(/[^a-z]/g, '');
        if (!w) return;

        let primaryViseme = 'viseme_aa';
        if (w.includes('o') || w.includes('u')) {
            primaryViseme = 'viseme_O';
        } else if (w.includes('i') || w.includes('e')) {
            primaryViseme = 'viseme_I';
        } else if (w.includes('m') || w.includes('p') || w.includes('b')) {
            primaryViseme = 'viseme_PP';
        }

        const duration = Math.min(420, Math.max(180, w.length * 65));

        this.currentWordBurst = {
            word: w,
            viseme: primaryViseme,
            startTime: performance.now(),
            duration: duration,
        };
        this.isSpeaking = true;
    }

    setSpeaking(isSpeaking, amplitude = 0, source = 'audio') {
        this.isSpeaking = isSpeaking;
        this.speechAmplitude = Math.max(0, Math.min(1, amplitude));
        this.speechSource = source;
        if (!isSpeaking) {
            this.currentWordBurst = null;
        }
    }

    animate() {
        if (this.disposed) return;
        this.animFrameId = requestAnimationFrame(this.animate);

        const delta = this.clock.getDelta();
        const time = this.clock.getElapsedTime();
        const now = performance.now();

        // 1. Natural Eyelid Blinking
        if (now > this.nextBlinkTime && this.blinkState === 0) {
            this.blinkState = 1;
            this.blinkStartTime = now;
        }

        let blinkValue = 0;
        if (this.blinkState === 1) {
            const elapsed = now - this.blinkStartTime;
            const progress = elapsed / this.blinkDuration;
            if (progress >= 1) {
                this.blinkState = 0;
                this.nextBlinkTime = now + 2400 + Math.random() * 3200;
            } else {
                blinkValue = Math.sin(progress * Math.PI);
            }
        }
        this.targetMorphs['eyeBlinkLeft'] = blinkValue;
        this.targetMorphs['eyeBlinkRight'] = blinkValue;
        this.targetMorphs['eyesClosed'] = blinkValue;

        // 2. Smooth Pointer / Gaze Tracking
        this.currentLook.x += (this.targetLook.x - this.currentLook.x) * 0.06;
        this.currentLook.y += (this.targetLook.y - this.currentLook.y) * 0.06;

        // 3. Idle Breathing & Micro-Head Motions
        const breathCycle = Math.sin(time * 1.6);
        const idleWanderY = Math.sin(time * 0.65) * 0.02;
        const idleWanderX = Math.cos(time * 0.5) * 0.012;
        const idleTilt = Math.sin(time * 0.45) * 0.008;

        const spine = this.bones['Spine2'] || this.bones['Spine1'] || this.bones['Spine'];
        if (spine && this.restRotations[spine.name]) {
            const rest = this.restRotations[spine.name];
            spine.rotation.x = rest.x + breathCycle * 0.01;
        }

        // Keep arms folded in Namaste pose synchronized with chest respiration
        this.applyNamastePose(breathCycle);

        const neck = this.bones['Neck'];
        if (neck && this.restRotations['Neck']) {
            const rest = this.restRotations['Neck'];
            neck.rotation.x = rest.x + breathCycle * 0.005 + this.currentLook.y * 0.04;
            neck.rotation.y = rest.y + this.currentLook.x * 0.06 + idleWanderY * 0.3;
        }

        const head = this.bones['Head'];
        if (head && this.restRotations['Head']) {
            const rest = this.restRotations['Head'];
            let headPitch = rest.x + idleWanderX + this.currentLook.y * 0.1;
            let headYaw = rest.y + idleWanderY + this.currentLook.x * 0.15;
            let headRoll = rest.z + idleTilt + this.currentLook.x * 0.03;

            // Subtle conversational head nod on vocal emphasis
            if (this.isSpeaking && (this.speechAmplitude > 0.08 || this.currentWordBurst)) {
                const nod = Math.sin(time * 9.0) * 0.025;
                headPitch += nod;
            }

            head.rotation.x = headPitch;
            head.rotation.y = headYaw;
            head.rotation.z = headRoll;
        }

        // 4. Accurate Syllable-Driven Lip-Sync
        let mouthOpenAmount = 0;
        let activeVisemeKey = 'viseme_aa';

        if (this.currentWordBurst) {
            const elapsed = now - this.currentWordBurst.startTime;
            const progress = elapsed / this.currentWordBurst.duration;
            if (progress <= 1.0) {
                // Syllable opening bell curve
                mouthOpenAmount = Math.sin(progress * Math.PI) * 0.85;
                activeVisemeKey = this.currentWordBurst.viseme;
            } else {
                this.currentWordBurst = null;
            }
        } else if (this.isSpeaking && this.speechAmplitude > 0.04) {
            // Audio frequency amplitude fallback
            mouthOpenAmount = Math.min(0.9, Math.pow(this.speechAmplitude * 1.6, 0.85));
            activeVisemeKey = 'viseme_aa';
        }

        if (mouthOpenAmount > 0.02) {
            // Apply balanced mouth opening (jaw + lips)
            this.targetMorphs['jawOpen'] = mouthOpenAmount * 0.55;
            this.targetMorphs['mouthOpen'] = mouthOpenAmount * 0.65;

            // Reset other visemes
            this.targetMorphs['viseme_aa'] = 0;
            this.targetMorphs['viseme_O'] = 0;
            this.targetMorphs['viseme_I'] = 0;
            this.targetMorphs['viseme_U'] = 0;
            this.targetMorphs['viseme_PP'] = 0;
            this.targetMorphs['viseme_FF'] = 0;
            this.targetMorphs['viseme_TH'] = 0;
            this.targetMorphs['viseme_SS'] = 0;

            // Trigger active viseme shape
            this.targetMorphs[activeVisemeKey] = mouthOpenAmount * 0.8;

            // Eyebrow emphasis
            this.targetMorphs['browInnerUp'] = 0.08 + mouthOpenAmount * 0.18;
            this.targetMorphs['mouthSmile'] = 0.05;
        } else {
            // Closed relaxed mouth with warm advisor smile
            this.targetMorphs['jawOpen'] = 0;
            this.targetMorphs['mouthOpen'] = 0;
            this.targetMorphs['viseme_aa'] = 0;
            this.targetMorphs['viseme_O'] = 0;
            this.targetMorphs['viseme_I'] = 0;
            this.targetMorphs['viseme_U'] = 0;
            this.targetMorphs['viseme_PP'] = 0;
            this.targetMorphs['viseme_FF'] = 0;
            this.targetMorphs['viseme_TH'] = 0;
            this.targetMorphs['viseme_SS'] = 0;
            this.targetMorphs['browInnerUp'] = 0.03;
            this.targetMorphs['mouthSmile'] = 0.14;
        }

        // 5. Lerp morph targets smoothly (snappy 0.45 while speaking for crisp articulation)
        const lerpRate = this.isSpeaking ? 0.45 : 0.22;
        for (const [key, target] of Object.entries(this.targetMorphs)) {
            const current = this.currentMorphs[key] || 0;
            const next = current + (target - current) * lerpRate;
            this.currentMorphs[key] = next;
            this.applyMorphToMeshes(key, next);
        }

        if (this.renderer && this.scene && this.camera) {
            this.renderer.render(this.scene, this.camera);
        }
    }

    applyMorphToMeshes(name, value) {
        for (let i = 0; i < this.morphMeshes.length; i++) {
            const mesh = this.morphMeshes[i];
            const dict = mesh.morphTargetDictionary;
            const influences = mesh.morphTargetInfluences;
            if (dict && influences && dict[name] !== undefined) {
                influences[dict[name]] = value;
            }
        }
    }

    disposeHierarchy(object) {
        if (!object) return;
        object.traverse((child) => {
            if (child.isMesh) {
                if (child.geometry) child.geometry.dispose();
                if (child.material) {
                    if (Array.isArray(child.material)) {
                        child.material.forEach((m) => m.dispose());
                    } else {
                        child.material.dispose();
                    }
                }
            }
        });
    }

    destroy() {
        this.disposed = true;
        if (this.animFrameId) {
            cancelAnimationFrame(this.animFrameId);
        }

        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
        }

        if (this.onPointerMove) {
            window.removeEventListener('mousemove', this.onPointerMove);
            window.removeEventListener('touchmove', this.onPointerMove);
        }
        if (this.onPointerLeave) {
            document.removeEventListener('mouseleave', this.onPointerLeave);
        }

        if (this.model) {
            this.disposeHierarchy(this.model);
            this.scene.remove(this.model);
        }

        if (this.renderer) {
            this.renderer.dispose();
            if (this.renderer.domElement && this.renderer.domElement.parentNode) {
                this.renderer.domElement.parentNode.removeChild(this.renderer.domElement);
            }
        }
    }
}

if (typeof window !== 'undefined') {
    window.Avatar3DViewer = Avatar3DViewer;
    window.create3DAvatar = (container, options) => new Avatar3DViewer(container, options);
}
