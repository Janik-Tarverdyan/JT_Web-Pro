<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Screen Sharing</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<script src="screen.js"></script>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <link rel="stylesheet" href="../css/ScreenSharing/style.css">

        <style>
            video {
                -moz-transition: all 1s ease;
                -ms-transition: all 1s ease;

                -o-transition: all 1s ease;
                -webkit-transition: all 1s ease;
                transition: all 1s ease;
                vertical-align: top;
                width: 100%;
            }
            input {
                border: 1px solid #d9d9d9;
                border-radius: 1px;
                font-size: 2em;
                margin: .2em;
                width: 30%;
            }
            select {
                border: 1px solid #d9d9d9;
                border-radius: 1px;
                height: 50px;
                margin-left: 1em;
                margin-right: -12px;
                padding: 1.1em;
                vertical-align: 6px;
                width: 18%;
            }
            .setup {
                border-bottom-left-radius: 0;
                border-top-left-radius: 0;
                font-size: 102%;
                height: 47px;
                margin-left: -9px;
                margin-top: 8px;
                position: absolute;
            }
            p { padding: 1em; }
            li {
                border-bottom: 1px solid rgb(189, 189, 189);
                border-left: 1px solid rgb(189, 189, 189);
                padding: .5em;
            }
        </style>
        <script>
            document.createElement('article');
            document.createElement('footer');
        </script>

        <link rel="chrome-webstore-item" href="http://localhost/Screen Sharing/plugin/Chrome-Extensions-master/desktopCapture/ajhifddimkapgcifgcodmmfdlknahffk">

        <!-- scripts used for screen-sharing -->
        <script src='../js/ScreenSharing/firebase.js'> </script>
        <script src="../js/ScreenSharing/getScreenId.js"> </script>
        <script src="../js/ScreenSharing/BandwidthHandler.js"></script>
        <script src="../js/ScreenSharing/Screen.js"> </script>
    </head>

    <body>
        <article>
	        <div class="github-stargazers"></div>

            <h2 id="number-of-participants" style="display: block;text-align: center;border:0;margin-bottom:0;">Its a Full-HD (1080p) Screen Sharing Application Using WebRTC </h2>

            <!-- just copy this <section> and next script -->
            <section class="experiment">
                <section>
                    <span>
                        Private <a href="/screen-sharing/" target="_blank" title="Open this link for private screen sharing!"><code><strong id="unique-token">#123456789</strong></code></a>
                    </span>
                    <input type="text" id="user-name" placeholder="Your Name">
                    <button id="share-screen" class="setup">Share Your Screen</button>
                </section>

                <!-- list of all available broadcasting rooms -->
                <table style="width: 100%;" id="rooms-list"></table>

                <!-- local/remote videos container -->
                <div id="videos-container"></div>
            </section>

            <script>
                function intallFirefoxScreenCapturingExtension() {
                    InstallTrigger.install({
                        'Foo': {
                            // URL: 'https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/',
                            URL: 'https://addons.mozilla.org/firefox/downloads/file/355418/enable_screen_capturing_in_firefox-1.0.006-fx.xpi?src=cb-dl-hotness',
                            toString: function() {
                                return this.URL;
                            }
                        }
                    });
                }

                var videosContainer = document.getElementById("videos-container") || document.body;
                var roomsList = document.getElementById('rooms-list');

                var screensharing = new Screen();

                screensharing.onscreen = function(_screen) {
                    var alreadyExist = document.getElementById(_screen.userid);
                    if (alreadyExist) return;
                    if (typeof roomsList === 'undefined') roomsList = document.body;
                    var tr = document.createElement('tr');

                    tr.id = _screen.userid;
                    tr.innerHTML = '<td>' + _screen.userid + ' shared his screen.</td>' +
                            '<td><button class="join">Preview His Screen</button></td>';
                    roomsList.insertBefore(tr, roomsList.firstChild);
                    var button = tr.querySelector('.join');
                    button.setAttribute('data-userid', _screen.userid);
                    button.setAttribute('data-roomid', _screen.roomid);
                    button.onclick = function() {
                        var button = this;
                        button.disabled = true;

                        var _screen = {
                            userid: button.getAttribute('data-userid'),
                            roomid: button.getAttribute('data-roomid')
                        };
                        screensharing.view(_screen);
                    };
                };
                // on getting each new screen
                screensharing.onaddstream = function(media) {
                    media.video.id = media.userid;

                    var video = media.video;
                    video.setAttribute('controls', true);
                    videosContainer.insertBefore(video, videosContainer.firstChild);
                    video.play();
                    rotateVideo(video);
                };
                // using firebase for signaling
                // screen.firebase = 'signaling';
                // if someone leaves; just remove his screen
                screensharing.onuserleft = function(userid) {
                    var video = document.getElementById(userid);
                    if (video && video.parentNode) video.parentNode.removeChild(video);
                };
                // check pre-shared screens
                screensharing.check();
                document.getElementById('share-screen').onclick = function() {
                    screensharing.share();
                    this.disabled = true;
                };

                document.getElementById('share-screen').onclick = function() {
                    var username = document.getElementById('user-name');
                    username.disabled = this.disabled = true;

                    screensharing.isModerator = true;
                    screensharing.userid = username.value;

                    screensharing.share();
                };

                function rotateVideo(video) {
                    video.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(0deg)';
                    setTimeout(function() {
                        video.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(360deg)';
                    }, 1000);
                }
                (function() {
                    var uniqueToken = document.getElementById('unique-token');
                    if (uniqueToken)
                        if (location.hash.length > 2) uniqueToken.parentNode.parentNode.parentNode.innerHTML = '<h2 style="text-align:center;"><a href="' + location.href + '" target="_blank">Share this link</a></h2>';
                        else uniqueToken.innerHTML = uniqueToken.parentNode.parentNode.href = '#' + (Math.random() * new Date().getTime()).toString(36).toUpperCase().replace( /\./g , '-');
                })();

                screensharing.onNumberOfParticipantsChnaged = function(numberOfParticipants) {
                    if(!screensharing.isModerator) return;

                    document.title = numberOfParticipants + ' users are viewing your screen!';
                    var element = document.getElementById('number-of-participants');
                    if (element) {
                        element.innerHTML = numberOfParticipants + ' users are viewing your screen!';
                    }
                };
            </script>

            <script>
                // todo: need to check exact chrome browser because opera also uses chromium framework
                var isChrome = !!navigator.webkitGetUserMedia;

                // DetectRTC.js - https://github.com/muaz-khan/WebRTC-Experiment/tree/master/DetectRTC
                // Below code is taken from RTCMultiConnection-v1.8.js (http://www.rtcmulticonnection.org/changes-log/#v1.8)
                var DetectRTC = {};
                (function () {

                    var screenCallback;

                    DetectRTC.screen = {
                        chromeMediaSource: 'screen',
                        getSourceId: function(callback) {
                            if(!callback) throw '"callback" parameter is mandatory.';
                            screenCallback = callback;
                            window.postMessage('get-sourceId', '*');
                        },
                        isChromeExtensionAvailable: function(callback) {
                            if(!callback) return;

                            if(DetectRTC.screen.chromeMediaSource == 'desktop') return callback(true);

                            // ask extension if it is available
                            window.postMessage('are-you-there', '*');

                            setTimeout(function() {
                                if(DetectRTC.screen.chromeMediaSource == 'screen') {
                                    callback(false);
                                }
                                else callback(true);
                            }, 2000);
                        },
                        onMessageCallback: function(data) {
                            if (!(typeof data == 'string' || !!data.sourceId)) return;

                            console.log('chrome message', data);

                            // "cancel" button is clicked
                            if(data == 'PermissionDeniedError') {
                                DetectRTC.screen.chromeMediaSource = 'PermissionDeniedError';
                                if(screenCallback) return screenCallback('PermissionDeniedError');
                                else throw new Error('PermissionDeniedError');
                            }

                            // extension notified his presence
                            if(data == 'rtcmulticonnection-extension-loaded') {
                                if(document.getElementById('install-button')) {
                                    document.getElementById('install-button').parentNode.innerHTML = '<strong>Great!</strong> <a href="http://localhost/Screen Sharing/plugin/Chrome-Extensions-master/desktopCapture/ajhifddimkapgcifgcodmmfdlknahffk" target="_blank">Google chrome extension</a> is installed.';
                                }
                                DetectRTC.screen.chromeMediaSource = 'desktop';
                            }

                            // extension shared temp sourceId
                            if(data.sourceId) {
                                DetectRTC.screen.sourceId = data.sourceId;
                                if(screenCallback) screenCallback( DetectRTC.screen.sourceId );
                            }
                        },
                        getChromeExtensionStatus: function (callback) {
                            if (!!navigator.mozGetUserMedia) return callback('not-chrome');

                            var extensionid = 'ajhifddimkapgcifgcodmmfdlknahffk';
                            var image = document.createElement('img');
                            image.src = 'chrome-extension://' + extensionid + '/icon.png';
                            image.onload = function () {
                                DetectRTC.screen.chromeMediaSource = 'screen';
                                window.postMessage('are-you-there', '*');
                                setTimeout(function () {
                                    if (!DetectRTC.screen.notInstalled) {
                                        callback('installed-enabled');
                                    }
                                }, 2000);
                            };
                            image.onerror = function () {
                                DetectRTC.screen.notInstalled = true;
                                callback('not-installed');
                            };
                        }
                    };

                    // check if desktop-capture extension installed.
                    if(window.postMessage && isChrome) {
                        DetectRTC.screen.isChromeExtensionAvailable();
                    }
                })();

                DetectRTC.screen.getChromeExtensionStatus(function(status) {
                    if(status == 'installed-enabled') {
                        if(document.getElementById('install-button')) {
                            document.getElementById('install-button').parentNode.innerHTML = '<strong>Great!</strong> Google chrome extension is installed.';
                        }
                        DetectRTC.screen.chromeMediaSource = 'desktop';
                    }
                });

                window.addEventListener('message', function (event) {
                    if (event.origin != window.location.origin) {
                        return;
                    }

                    DetectRTC.screen.onMessageCallback(event.data);
                });

                console.log('current chromeMediaSource', DetectRTC.screen.chromeMediaSource);
            </script>

            <section class="experiment">
                <h2>Prerequisites</h2>
                <ol>
                    <li>
                        Chrome or Opera?
                        <a href="plugin/Chrome-Extensions-master/">Source Code</a>

                        <button onclick="!!navigator.webkitGetUserMedia && !!window.chrome && !!chrome.webstore && !!chrome.webstore.install && chrome.webstore.install('http://localhost/Screen Sharing/plugin/Chrome-Extensions-master/desktopCapture/', function() {location.reload();})" id="install-button" style="font-size:inherit; padding-bottom:0;">Click to Install</button>

                    </li>

                    <li>
                        Firefox? <a href="https://addons.mozilla.org/en-US/firefox/addon/enable-screen-capturing/">Store</a> / <a href="https://github.com/muaz-khan/Firefox-Extensions/tree/master/enable-screen-capturing">Source Code</a> / <button onclick="intallFirefoxScreenCapturingExtension(); this.disabled = true;" style="font-size:inherit; padding-bottom:0;">Click to Install</button>
                    </li>
                </ol>
            </section>

        </article>
    </body>
</html>
