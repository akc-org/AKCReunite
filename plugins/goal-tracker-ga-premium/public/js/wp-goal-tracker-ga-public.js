var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var gtgaMainJs = (function ($) {
    'use strict';
    var _this = this;
    /**
     * Binds Download Tracking
     *
     * @returns {undefined}
     */
    var VideoPercent = {
        ZERO: 0,
        TWENTYFIVE: 25,
        FIFTY: 50,
        SEVENTYFIVE: 75,
        ONEHUNDRED: 100,
    };
    function elementAddedCallback(addedNode) {
        checkVisibilityEvents();
    }
    var observer = new MutationObserver(function (mutationsList) {
        mutationsList.forEach(function (mutation) {
            if (mutation.type === 'childList') {
                Array.prototype.forEach.call(mutation.addedNodes, function (addedNode) {
                    if (addedNode.nodeType === Node.ELEMENT_NODE) {
                        elementAddedCallback(addedNode);
                    }
                });
            }
        });
    });
    function getLinkClickParameters(event, url) {
        var tmpURL = new URL(url);
        var linkHostname = tmpURL.hostname;
        var linkEvent = {
            page_title: wpGoalTrackerGa.pageTitle,
            link_url: url,
            // page_location: window.location.href,
            outbound: isLinkExternal(url),
            link_domain: linkHostname,
            link_text: $(event.target).text(),
            link_classes: $(event.target).attr('class'),
        };
        return linkEvent;
    }
    var click_event = function (event) {
        trackCustomEvent(this, event.data.eventName, event.data.props);
        if ((typeof event.target.href !== 'undefined' &&
            event.target.nodeName == 'A') ||
            (typeof event.currentTarget.href !== 'undefined' &&
                event.currentTarget.nodeName == 'A')) {
            if ($(event.target).parent().attr('role') !== 'tab') {
                handleLinks(this, event);
            }
        }
    }; // End of click event function
    function bindEmailLinksTracking() {
        if (wpGoalTrackerGa.trackEmailLinks === '1') {
            $('body').on('click', 'a[href^="mailto:"]', function (e) {
                e.preventDefault();
                var email = this.href.split(':').pop();
                var page = getPageName();
                var eventParameters = {
                    page_title: page,
                    email_address: email,
                    page_location: window.location.href,
                    link_text: $(e.target).text(),
                    link_classes: $(e.target).attr('class'),
                };
                trackCustomEvent(this, 'email_link_click', eventParameters);
                handleLinks(this, e);
            });
        }
    }
    function trackPageSpeed() {
        if (window.performance &&
            window.performance.getEntriesByType &&
            wpGoalTrackerGa.pageSpeedTracking) {
            var entries = window.performance.getEntriesByType('navigation');
            var pageLoadTime = 0;
            if (entries.length > 0) {
                var navTiming = entries[0];
                pageLoadTime = navTiming.loadEventEnd - navTiming.startTime;
            }
            if (pageLoadTime > 0) {
                var pageLoadTimeSec = Math.round((pageLoadTime / 1000 + Number.EPSILON) * 100) / 100;
                trackCustomEvent(this, 'page_load_time', {
                    page_load_time: pageLoadTimeSec,
                });
            }
        }
    } // End of page speed event function
    window.onload = function () {
        if (wpGoalTrackerGa.pageSpeedTracking) {
            setTimeout(function () {
                trackPageSpeed();
            }, 100);
        }
    };
    var isLinkExternal = function (url) {
        var query = new RegExp('//' + location.host + '($|/)');
        if (url.substring(0, 4) === 'http') {
            if (!query.test(url)) {
                return true;
            }
        }
        return false;
    };
    var link_track_external = function (event) {
        var url = getUrl(event);
        if (typeof url !== 'undefined' && url !== '') {
            if (isLinkExternal(url)) {
                link_track_all(event);
            }
        }
    };
    var link_track_external_new_tab = function (event) {
        var url = getUrl(event);
        if (isLinkExternal(url)) {
            var eventParameters = getLinkClickParameters(event, url);
            trackCustomEvent(this, 'link_click', eventParameters);
        }
    };
    var link_track_all = function (event) {
        var url = getUrl(event);
        var hash = isJustHashLink(url);
        if (typeof url !== 'undefined' &&
            url !== '' &&
            hash != '#' &&
            $(this).parent().attr('role') !== 'tab') {
            var eventParameters = getLinkClickParameters(event, url);
            trackCustomEvent(this, 'link_click', eventParameters);
            event.preventDefault();
            if (typeof hash !== 'undefined' && hash !== '') {
                window.location.hash = hash;
            }
            else {
                setTimeout(function () {
                    window.location.href = url;
                }, 250);
            }
        }
    };
    var link_track_all_new_tab = function (event) {
        var url = getUrl(event);
        if (typeof url !== 'undefined' && url !== '') {
            var eventParameters = getLinkClickParameters(event, url);
            trackCustomEvent(this, 'link_click', eventParameters);
        }
    };
    var handleLinks = function (self, event) {
        event.preventDefault();
        var link = getUrl(event);
        if (link === '')
            return;
        var w;
        var openInNewTab = isNewTab(self);
        if (openInNewTab) {
            w = window.open('', '_blank');
        }
        var hash = isJustHashLink(link);
        if (typeof hash !== 'undefined' && hash !== '') {
            window.location.hash = hash;
        }
        else if (window.location.href !== link) {
            setTimeout(function () {
                if (openInNewTab) {
                    w.location.href = link;
                }
                else {
                    window.location.href = link;
                }
            }, 250, w);
        }
    };
    var getUrl = function (event) {
        var url = '';
        var $target = $(event.target);
        var $link = $target.closest('a');
        if ($link.length) {
            var href = $link.attr('href');
            if (href && href !== '#') {
                url = $link.prop('href');
            }
        }
        return url;
    };
    var isJustHashLink = function (url) {
        if (url.indexOf('#') === 0) {
            return url;
        }
        var currentUrl = new URL(window.location.href);
        var targetUrl = new URL(url, currentUrl);
        if (targetUrl.origin !== currentUrl.origin) {
            return '';
        }
        if (targetUrl.pathname === currentUrl.pathname &&
            targetUrl.search === currentUrl.search &&
            targetUrl.hash !== '') {
            return targetUrl.hash;
        }
        return '';
    };
    var isNewTab = function (self) {
        var target = $(self).attr('target');
        if (typeof target !== 'undefined' && target.trim() === '_blank') {
            return true;
        }
        return false;
    };
    $(document).ready(function () {
        var targetNode = document.body;
        var config = { childList: true, subtree: true };
        observer.observe(targetNode, config);
        $(window).on('scroll', checkVisibilityEvents);
        // We also want to check it when
        checkVisibilityEvents();
        if (wpGoalTrackerGa.trackEmailLinks) {
            bindEmailLinksTracking();
        }
        // Bind link tracking events
        if (wpGoalTrackerGa.trackLinks.enabled) {
            if (wpGoalTrackerGa.trackLinks.type === 'all') {
                $('body').on('click', "a:not([target~='_blank']):not(.video_popup):not(.dtq-video-popup-trigger):not(:has(.video_popup)):not(.video_popup *)", link_track_all);
                $('body').on('click', "a[target~='_blank']:not(.video_popup):not(.dtq-video-popup-trigger):not(:has(.video_popup)):not(.video_popup *)", link_track_all_new_tab);
            }
            else if (wpGoalTrackerGa.trackLinks.type === 'external') {
                $('body').on('click', "a:not([target~='_blank']):not(.video_popup):not(.dtq-video-popup-trigger):not(:has(.video_popup)):not(.video_popup *)", link_track_external);
                $('body').on('click', "a[target~='_blank']:not(.video_popup):not(.dtq-video-popup-trigger):not(:has(.video_popup)):not(.video_popup *)", link_track_external_new_tab);
            }
        }
        wpGoalTrackerGa.click.forEach(function (el) {
            var selector = makeSelector(el);
            $('body').on('click', selector, el, click_event);
        });
    });
    function makeSelector(click_option) {
        var selector = '';
        if (click_option.selectorType === 'class') {
            selector += '.';
        }
        else if (click_option.selectorType === 'id') {
            selector += '#';
        }
        selector += click_option.selector;
        return selector;
    }
    function checkVisibilityEvents() {
        // TO DO this code can be simplified a lot. May be better to use
        // $('element').visibility()
        var ga_window = $(window).height();
        var ga_visibility_top = $(document).scrollTop();
        for (var i = 0; i < wpGoalTrackerGa.visibility.length; i++) {
            if (!wpGoalTrackerGa.visibility[i].sent) {
                // NB was unescapeChars( wpGoalTrackerGa.visibility[i].select)
                var $select = $(makeSelector(wpGoalTrackerGa.visibility[i]));
                wpGoalTrackerGa.visibility[i].offset = $select.offset();
                if (wpGoalTrackerGa.visibility[i].offset &&
                    ga_visibility_top + ga_window >=
                        wpGoalTrackerGa.visibility[i].offset.top + $select.height()) {
                    trackCustomEvent($select, wpGoalTrackerGa.visibility[i].eventName, wpGoalTrackerGa.visibility[i].props);
                    wpGoalTrackerGa.visibility[i].sent = true;
                }
            }
        }
    } // End of bindVisibilityEvents
    var trackCustomEventBasic = function (self, name, props) {
        Object.keys(props).forEach(function (key) {
            props[key] = prepareProps(self, props[key]);
        });
        gtag('event', name, __assign({}, props));
    };
    /* <fs_premium_only> */
    /* ---------------------------------------------------------------- */
    var trackCustomEventPro = function (self, name, props) {
        // We don't want to override the original object.
        var propsCopy = __assign({}, props);
        Object.keys(propsCopy).forEach(function (key) {
            propsCopy[key] = prepareProps(self, propsCopy[key]);
        });
        // testLocalEvent(this, name, props);
        if (typeof gtgatag !== 'undefined') {
            gtgatag('event', name, propsCopy);
        }
        else if (typeof gtag !== 'undefined') {
            gtag('event', name, propsCopy);
        }
    };
    var onPlayerReady = function () { };
    var youtube_video_instances = [];
    var youTubeApiIsReady = false;
    var youTubeApiCallbacks = [];
    function whenYouTubeApiReady(callback) {
        if (youTubeApiIsReady) {
            callback();
        }
        else {
            youTubeApiCallbacks.push(callback);
        }
    }
    function register_youtube_video(video) {
        whenYouTubeApiReady(function () {
            // YouTube API is ready, you can now register the video
            youtube_video_instances.push(new YT.Player(video, {
                events: {
                    onReady: onPlayerReady,
                    onStateChange: onPlayerStateChange,
                    onPlaybackQualityChange: onPlaybackQualityChange,
                },
            }));
        });
    }
    // Tracking YouTube Porgress using interval checking.
    function trackYouTubeProgress(player) {
        var playerInfo = player.playerInfo; // Assuming playerInfo is always updated and reliable
        var mediaDuration = playerInfo.duration;
        var timeoutDuration = (mediaDuration * 1000) / 20;
        var count = 0;
        var eventsSent = [];
        var durationInterval = setInterval(function () {
            var playerState = playerInfo.playerState;
            if (playerState === YT.PlayerState.PLAYING) {
                count += 1;
                var currentTime = playerInfo.currentTime;
                var currentPos = (currentTime / mediaDuration) * 100;
                var roundedPos = getVideoPosition(currentPos, eventsSent);
                if (!eventsSent[roundedPos]) {
                    // Check to avoid re-sending events for the same position
                    eventsSent[roundedPos] = true;
                    if (roundedPos !== 0) {
                        var videoData = playerInfo.videoData; // Assuming videoData has all required video info
                        var eventData = {
                            video_current_time: currentTime,
                            video_duration: mediaDuration,
                            video_percent: roundedPos,
                            video_title: videoData.title,
                            video_url: "https://www.youtube.com/watch?v=".concat(videoData.video_id),
                            video_provider: 'youtube',
                        };
                        trackCustomEvent(player, 'video_progress', eventData);
                    }
                }
            }
            else {
                clearInterval(durationInterval);
            }
            if (count >= 21 || eventsSent[75]) {
                clearInterval(durationInterval);
            }
        }, timeoutDuration);
    }
    // youtube video player
    function onPlayerStateChange(event) {
        var player = event.target;
        var playerInfo = player.playerInfo;
        // Extract necessary data directly from playerInfo if other methods are unavailable
        var videoData = playerInfo.videoData;
        var playerStatus = playerInfo.playerState;
        var currentVideoUrl = "https://www.youtube.com/watch?v=".concat(videoData.video_id);
        var duration = playerInfo.duration;
        // Check if the video has just started playing
        if (playerStatus === YT.PlayerState.PLAYING ||
            (playerStatus === -1 && playerInfo.currentTime === 0)) {
            var eventData = {
                video_current_time: playerInfo.currentTime,
                video_duration: duration,
                video_percent: VideoPercent.ZERO,
                video_title: videoData.title,
                video_url: currentVideoUrl,
                video_provider: 'youtube',
            };
            trackCustomEvent(this, 'video_start', eventData);
            trackYouTubeProgress(player); // Assuming this function is adjusted for when direct player methods are missing
        }
        else if (playerStatus === YT.PlayerState.ENDED) {
            // When the video ends
            var eventData = {
                video_current_time: duration,
                video_duration: duration,
                video_percent: VideoPercent.ONEHUNDRED,
                video_title: videoData.title,
                video_url: currentVideoUrl,
                video_provider: 'youtube',
            };
            trackCustomEvent(this, 'video_complete', eventData);
        }
        else if (playerStatus === YT.PlayerState.PAUSED) {
            // Handle pause event, if necessary
        }
    }
    function onPlaybackQualityChange(event) {
        var iframe = event.target.getIframe();
    }
    // get youtube id from src
    var youtube_src_parser = function (url) {
        var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
        var match = url.match(regExp);
        return match && match[7].length === 11 ? match[7] : false;
    };
    // get vimeo id from src
    var vimeo_src_parser = function (url) {
        var regExp = /(videos|video|channels|\.com)\/([\d]+)/;
        var match = url.match(regExp)[2];
        return match ? match : false;
    };
    // add url param
    var add_params_to_url = function (url, param) {
        var new_url;
        if (-1 === url.indexOf('?')) {
            new_url = url + '?' + param;
        }
        else {
            new_url = url + '&' + param;
        }
        return new_url;
    };
    function checkForVimeoVideos() {
        var iframes = document.querySelectorAll('iframe');
        iframes.forEach(function (iframe) {
            var src = iframe.getAttribute('src');
            var dataLazySrc = iframe.getAttribute('data-lazy-src');
            var dataSrc = iframe.getAttribute('data-src');
            if (src && src.includes('vimeo.com')) {
                var vimeo_id = vimeo_src_parser(src);
                $(iframe).attr('id', vimeo_id); //force replace the iframe ID with the vimeo_id
                register_vimeo_video(vimeo_id);
            }
            else if (dataLazySrc || dataSrc) {
                // set a timer to wait for iframe to load
                var intervalId_1 = setInterval(function () {
                    var newSrc = iframe.getAttribute('src'); // get the updated src attribute
                    var newDataLazySrc = iframe.getAttribute('data-lazy-src'); // get the updated data-lazy-src attribute
                    var newDataSrc = iframe.getAttribute('data-src'); // get the updated data-lazy-src attribute
                    if (newSrc && newSrc.includes('vimeo.com')) {
                        // `src` attribute has been set, register the video
                        var vimeo_id = vimeo_src_parser(newSrc);
                        $(iframe).attr('id', vimeo_id); //force replace the iframe ID with the vimeo_id
                        register_vimeo_video(vimeo_id);
                        // clear the interval
                        clearInterval(intervalId_1);
                    }
                    else if (!newDataLazySrc && !newDataSrc) {
                        // data-lazy-src attribute is no longer present, clear the interval
                        clearInterval(intervalId_1);
                    }
                }, 1000); // check every 1 second
            }
        });
    }
    // check for videos that do not have iframe id, origin or jsapi
    function checkForYoutubeVideos() {
        var iframes = document.querySelectorAll('iframe');
        iframes.forEach(function (iframe) {
            var src = iframe.getAttribute('src');
            var dataLazySrc = iframe.getAttribute('data-lazy-src');
            var dataSrc = iframe.getAttribute('data-src');
            if (src && src.includes('youtube.com')) {
                // Regular case: src is already set
                registerAndModifyIframe(iframe, src);
            }
            else if (dataLazySrc || dataSrc) {
                // Lazy loading case: set an interval to check for iframe src updates
                var intervalId_2 = setInterval(function () {
                    var newSrc = iframe.getAttribute('src');
                    if (newSrc && newSrc.includes('youtube.com')) {
                        registerAndModifyIframe(iframe, newSrc);
                        clearInterval(intervalId_2); // Clear the interval once we've found and registered the video
                    }
                }, 1000); // Check every second
            }
        });
    }
    function registerAndModifyIframe(iframe, src) {
        var youtube_id = youtube_src_parser(src);
        var new_iframe_src = src;
        // Check and set iframe ID if missing
        if (!iframe.id) {
            iframe.id = youtube_id;
        }
        // Check and set jsapi parameter if missing
        if (src.indexOf('jsapi') === -1) {
            new_iframe_src = add_params_to_url(new_iframe_src, 'enablejsapi=1');
        }
        // Check and set origin parameter if missing
        if (src.indexOf('origin') === -1) {
            var originValue = window.location.protocol + '//' + window.location.hostname;
            if (window.location.port) {
                originValue += ':' + window.location.port;
            }
            new_iframe_src = add_params_to_url(new_iframe_src, 'origin=' + originValue);
        }
        // Update iframe src if it was modified
        if (new_iframe_src !== src) {
            iframe.src = new_iframe_src;
        }
        // Register the video for tracking
        register_youtube_video(iframe.id);
    }
    function checkForElementorLazyLoadedVideos() {
        var $elements = $('.elementor-widget-video');
        $elements.each(function () {
            var settings = JSON.parse($(this).attr('data-settings'));
            var $element = $(this);
            if (settings.video_type === 'youtube') {
                var checkIframe = function () {
                    // find one iframe
                    var $iframe = $element.find('iframe');
                    if ($iframe.length > 0) {
                        registerAndModifyIframe($iframe[0], $iframe[0].src);
                        clearInterval(intervalId);
                    }
                };
                var intervalId = setInterval(checkIframe, 1000);
            }
        });
    }
    function register_vimeo_video(video) {
        var vimeo_player = new Vimeo.Player(video);
        vimeo_player.gtgaVideoPercentSent = [];
        vimeo_player.on('play', vimeoOnPlay);
        vimeo_player.on('ended', vimeoOnEnd);
        vimeo_player.on('timeupdate', vimeoOnTimeUpdate);
    }
    function vimeoOnTimeUpdate(event) {
        var _this = this;
        var percent = 0;
        var roundedPercent = event.percent * 100;
        if (roundedPercent > 75 && !this.gtgaVideoPercentSent[75]) {
            percent = 75;
        }
        else if (roundedPercent > 50 && !this.gtgaVideoPercentSent[50]) {
            percent = 50;
        }
        else if (roundedPercent > 25 && !this.gtgaVideoPercentSent[25]) {
            percent = 25;
        }
        else if (roundedPercent > 10 && !this.gtgaVideoPercentSent[10]) {
            percent = 10;
        }
        else {
            return;
        }
        if (percent != 0) {
            var videoDuration = event.duration;
            var vimeoEvent_1 = {
                video_current_time: event.seconds,
                video_duration: videoDuration,
                video_title: '',
                video_percent: percent,
                video_provider: 'vimeo',
                video_url: '',
            };
            this.getVideoTitle().then(function (title) {
                vimeoEvent_1.video_title = title;
                _this.getVideoUrl().then(function (url) {
                    vimeoEvent_1.video_url = url;
                    trackCustomEvent(_this, 'video_progress', vimeoEvent_1);
                    _this.gtgaVideoPercentSent[percent] = true;
                });
            });
        }
    }
    function vimeoOnPlay(event) {
        var _this = this;
        var videoDuration = event.duration;
        var vimeoEvent = {
            video_current_time: 0,
            video_duration: videoDuration,
            video_title: '',
            video_percent: 0,
            video_provider: 'vimeo',
            video_url: '',
        };
        this.getVideoTitle().then(function (title) {
            vimeoEvent.video_title = title;
            _this.getVideoUrl().then(function (url) {
                vimeoEvent.video_url = url;
                trackCustomEvent(_this, 'video_start', vimeoEvent);
            });
        });
    }
    function vimeoOnEnd(event) {
        var _this = this;
        var videoDuration = event.duration;
        var vimeoEvent = {
            video_current_time: videoDuration,
            video_duration: videoDuration,
            video_title: '',
            video_percent: 100,
            video_provider: 'vimeo',
            video_url: '',
        };
        this.getVideoTitle().then(function (title) {
            vimeoEvent.video_title = title;
            _this.getVideoUrl().then(function (url) {
                vimeoEvent.video_url = url;
                trackCustomEvent(_this, 'video_complete', vimeoEvent);
            });
        });
    }
    ////////////////////////////////////////////////////////
    ///// Self Hosted Media Tracking 										  //
    ////////////////////////////////////////////////////////
    function capitalizeFirstLetter(word) {
        return word.charAt(0).toUpperCase() + word.slice(1);
    }
    function getMediaFileName(e) {
        if (typeof e.target !== 'undefined' && e.target.currentSrc) {
            return e.target.currentSrc.replace(/^.*[\\\/]/, '');
        }
        else if (typeof e.target !== 'undefined' && e.target.src) {
            return e.target.src.replace(/^.*[\\\/]/, '');
        }
        return '';
    }
    function getMediaEvent(type, currentTime, duration, title, percent, url) {
        if (type === 'video') {
            var mediaEvent = {
                video_current_time: currentTime,
                video_duration: duration,
                video_title: title,
                video_percent: percent,
                video_provider: 'self_hosted',
                video_url: url,
            };
            return mediaEvent;
        }
        if (type === 'audio') {
            var mediaEvent = {
                audio_current_time: currentTime,
                audio_duration: duration,
                audio_title: title,
                audio_percent: percent,
                audio_provider: 'self_hosted',
                audio_url: url,
            };
            return mediaEvent;
        }
        return;
    }
    function register_media_tracking(type) {
        if ((type === 'video' &&
            typeof wpGoalTrackerGa.videoSettings.gaMediaVideoTracking !==
                'undefined') ||
            (type === 'audio' &&
                typeof wpGoalTrackerGa.videoSettings.gaMediaAudioTracking)) {
            document.addEventListener('play', function (e) {
                if (e.target.tagName === type.toUpperCase()) {
                    var url = e.target.currentSrc;
                    var title = getMediaFileName(e);
                    var duration = e.target.duration;
                    var mediaEvent = getMediaEvent(type, 0, duration, title, 0, url);
                    trackCustomEvent(this, type + '_start', mediaEvent);
                }
            }, true);
            document.addEventListener('ended', function (e) {
                if (e.target.tagName === type.toUpperCase()) {
                    var url = e.target.currentSrc;
                    var title = getMediaFileName(e);
                    var duration = e.target.duration;
                    var mediaEvent = getMediaEvent(type, duration, duration, title, 100, url);
                    trackCustomEvent(this, type + '_complete', mediaEvent);
                }
            }, true);
            document.addEventListener('timeupdate', function (e) {
                if (e.target.tagName === type.toUpperCase()) {
                    if (typeof e.target.progressSent === 'undefined') {
                        e.target.progressSent = [];
                    }
                    var media = e.target;
                    var mediaDuration = e.target.duration;
                    var currentPos = (media.currentTime / mediaDuration) * 100;
                    var roundedPos = getVideoPosition(currentPos, e.target.progressSent);
                    if (roundedPos !== 0) {
                        e.target.progressSent[roundedPos] = true;
                        var url = e.target.currentSrc;
                        var title = getMediaFileName(e);
                        var currentTime = e.target.currentTime;
                        var duration = e.target.duration;
                        var mediaEvent = getMediaEvent(type, currentTime, duration, title, roundedPos, url);
                        trackCustomEvent(this, type + '_progress', mediaEvent);
                    }
                }
            }, true);
        }
    }
    ////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////
    ///// WooCommerce 										  //
    ////////////////////////////////////////////////////////
    function trackWooCommerceEvents() {
        var wooCommerceSettings = wpGoalTrackerGa.ecommerceTrackingSettings.wooCommerceSettings;
        if (wooCommerceSettings['addShippingInfo'])
            trackWooCommerceAddShippingInfo();
        if (wooCommerceSettings['addPaymentInfo'])
            trackWooCommerceAddPaymentInfo();
        if (wooCommerceSettings['addToCart'])
            trackWooCommerceAddToCartAjax();
        // if (wooCommerceSettings['viewCart']) trackWooCommerceViewCart();
        if (wooCommerceSettings['beginCheckout'])
            trackWooCommerceBeginCheckout();
    }
    function getCartDetails(callback) {
        jQuery.ajax({
            url: '/wp-json/wc/store/cart',
            method: 'GET',
            success: function (cart) {
                if (cart && cart.items) {
                    var items = cart.items.map(function (item) { return ({
                        item_id: item.id,
                        item_name: item.name,
                        quantity: item.quantity,
                        price: item.prices.price,
                    }); });
                    var totalValue = cart.totals.total_price || 0;
                    var currency = cart.currency || 'USD';
                    var eventData = {
                        currency: currency,
                        value: totalValue,
                        items: items,
                    };
                    callback(eventData);
                }
            },
            error: function (error) {
                console.error('Error fetching cart data:', error);
            },
        });
    }
    // function trackWooCommerceViewCart() {
    //   jQuery(document.body).on(
    //     'focus',
    //     '.wc-block-mini-cart__button',
    //     function () {
    //       getCartDetails(eventData => {
    //         trackCustomEvent(this, 'view_cart', eventData);
    //       });
    //     },
    //   );
    // }
    function trackWooCommerceAddToCartAjax() {
        var _this = this;
        if (typeof wp !== 'undefined' && typeof wp.hooks !== 'undefined') {
            wp.hooks.addAction('experimental__woocommerce_blocks-cart-add-item', 'wp_goal_tracker_ga', function (_a) {
                var product = _a.product;
                var eventData = {
                    currency: product.currency,
                    value: product.totals.total_price,
                    items: product.items.map(function (item) { return ({
                        item_id: item.id,
                        item_name: item.name,
                        quantity: item.quantity,
                        price: item.prices.price,
                    }); }),
                };
                trackCustomEvent(_this, 'add_to_cart', eventData);
            });
        }
        jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
            var product_id = $button.data('product_id');
            var product_sku = $button.data('product_sku');
            var product_name = $button.data('product_name');
            var product_price = $button.data('product_price');
            var product_currency = $button.data('product_currency') || 'USD';
            if (product_id && product_name && product_price) {
                var eventData = {
                    currency: product_currency,
                    value: product_price,
                    items: [
                        {
                            item_id: product_sku || product_id,
                            item_name: product_name,
                            price: product_price,
                            currency: product_currency,
                            quantity: 1,
                        },
                    ],
                };
                trackCustomEvent(this, 'add_to_cart', eventData);
            }
        });
    }
    function trackWooCommerceBeginCheckout() {
        var _this = this;
        if (typeof wp !== 'undefined' && typeof wp.hooks !== 'undefined') {
            wp.hooks.addAction('experimental__woocommerce_blocks-checkout-render-checkout-form', 'wp_goal_tracker_ga', function (_a) {
                var storeCart = _a.storeCart;
                var eventData = {
                    currency: storeCart.currency,
                    value: storeCart.totals.total_price,
                    items: storeCart.items.map(function (item) { return ({
                        item_id: item.id,
                        item_name: item.name,
                        quantity: item.quantity,
                        price: item.prices.price,
                    }); }),
                };
                trackCustomEvent(_this, 'begin_checkout', eventData);
            });
        }
    }
    function trackWooCommerceAddShippingInfo() {
        var _this = this;
        var shippingInfoSent = false;
        if (typeof wp !== 'undefined' && typeof wp.hooks !== 'undefined') {
            wp.hooks.addAction('experimental__woocommerce_blocks-checkout-set-shipping-address', 'wp_goal_tracker_ga', function (_a) {
                var storeCart = _a.storeCart;
                var eventData = {
                    currency: storeCart.currency,
                    value: storeCart.totals.total_price,
                    items: storeCart.items.map(function (item) { return ({
                        item_id: item.id,
                        item_name: item.name,
                        quantity: item.quantity,
                        price: item.prices.price,
                    }); }),
                };
                if (!shippingInfoSent) {
                    shippingInfoSent = true;
                    trackCustomEvent(_this, 'add_shipping_info', eventData);
                }
            });
        }
        else {
            $('.woocommerce-billing-fields, .woocommerce-shipping-fields, .shipping').on('focus', 'input', function (event) {
                if (!shippingInfoSent) {
                    var eventData = [];
                    if (typeof wpGoalTrackerWooData !== 'undefined') {
                        eventData = wpGoalTrackerWooData;
                    }
                    trackCustomEvent(this, 'add_shipping_info', eventData);
                    shippingInfoSent = true;
                }
            });
        }
    }
    function trackWooCommerceAddPaymentInfo() {
        var _this = this;
        if (typeof wp !== 'undefined' && typeof wp.hooks !== 'undefined') {
            wp.hooks.addAction('experimental__woocommerce_blocks-checkout-submit', 'wp_goal_tracker_ga', function (_a) {
                var storeCart = _a.storeCart;
                var eventData = {
                    currency: storeCart.currency,
                    value: storeCart.totals.total_price,
                    items: storeCart.items.map(function (item) { return ({
                        item_id: item.id,
                        item_name: item.name,
                        quantity: item.quantity,
                        price: item.prices.price,
                    }); }),
                };
                trackCustomEvent(_this, 'add_payment_info', eventData);
            });
        }
        else {
            $('.checkout').on('submit', function (e) {
                var eventData = [];
                if (typeof wpGoalTrackerWooData !== 'undefined') {
                    eventData = wpGoalTrackerWooData;
                }
                trackCustomEvent(this, 'add_payment_info', eventData);
            });
        }
    }
    ////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////
    ////// Handle Placeholders													 ///////////
    ////////////////////////////////////////////////////////////////
    function get_placeholder(self, placeholder) {
        if (typeof placeholder === 'undefined') {
            return '';
        }
        var el = placeholder;
        var lel = '';
        if (typeof el.indexOf !== 'function') {
            return placeholder;
        }
        if (el.indexOf('$$PAGENAME$$') > -1) {
            if (true === wpGoalTrackerGa.isFrontPage) {
                el = replace_with(el, '$$PAGENAME$$', 'Home page');
            }
            else {
                el = replace_with(el, '$$PAGENAME$$', wpGoalTrackerGa.pageTitle);
            }
        }
        if (el.indexOf('$$POST_ID$$') > -1) {
            el = replace_with(el, '$$POST_ID$$', wpGoalTrackerGa.postID);
        }
        if (el.indexOf('$$CATEGORY$$') > -1) {
            el = replace_with(el, '$$CATEGORY$$', wpGoalTrackerGa.category);
        }
        if (el.indexOf('$$ATTR_') > -1) {
            var attr = '';
            var regex = /\$\$ATTR_(.*)\$\$/g;
            var match = regex.exec(el);
            if (typeof match[1] !== 'undefined') {
                attr = match[1].toLowerCase();
            }
            lel = $(this).attr(attr);
            if (typeof lel === 'undefined') {
                lel = $(self).attr(attr);
            }
            el = replace_with(el, match[0], lel);
        }
        if (el.indexOf('$$ELEMENT_TEXT$$') > -1) {
            el = replace_with(el, '$$ELEMENT_TEXT$$', $(self).text());
        }
        if (el.indexOf('$$AUTHOR$$') > -1) {
            el = replace_with(el, '$$AUTHOR$$', wpGoalTrackerGa.postAuthor);
        }
        if (el.indexOf('$$REFERRER$$') > -1) {
            var referrer = document.referrer;
            if (referrer === '') {
                referrer = window.location.href;
            }
            el = replace_with(el, '$$REFERRER$$', referrer);
        }
        if (el.indexOf('$$USER$$') > -1) {
            if (0 === wpGoalTrackerGa.currentUserName) {
                lel = 'Guest';
            }
            else {
                lel = wpGoalTrackerGa.currentUserName;
            }
            el = replace_with(el, '$$USER$$', lel);
        }
        if (el.indexOf('$$PAGE_URL$$') > -1) {
            el = window.location.href;
        }
        if (el.indexOf('$$IS_LOGGED_IN$$') > -1) {
            if (typeof wpGoalTrackerGa.isUserLoggedIn !== 'undefined' &&
                wpGoalTrackerGa.isUserLoggedIn === '1') {
                el = 'true';
            }
            else {
                el = 'false';
            }
        }
        if (el.indexOf('$$USER_ID$$') > -1) {
            if (typeof wpGoalTrackerGa.currentUserId !== 'undefined') {
                el = wpGoalTrackerGa.currentUserId;
            }
            else {
                el = '';
            }
        }
        return el;
    }
    var replace_with = function (el, search, updated) {
        return el.replace(search, updated);
    };
    var sendQueuedEvents = function () {
        if (typeof wpGoalTrackerGaEvents !== 'undefined' &&
            typeof wpGoalTrackerGaEvents.pending !== 'undefined') {
            Object.keys(wpGoalTrackerGaEvents.pending).forEach(function (key) {
                if (typeof wpGoalTrackerGaEvents.pending[key] !== 'undefined' &&
                    Object.keys(wpGoalTrackerGaEvents.pending[key]).length > 0) {
                    var eventData = wpGoalTrackerGaEvents.pending[key];
                    trackCustomEvent(this, key, eventData);
                }
            });
        }
    };
    // function testLocalEvent(self, eventName, eventData) {
    //   // Send a request to a local server to test the event. use localhost:8989
    //   jQuery.ajax({
    //     url: 'http://localhost:8989/',
    //     method: 'POST',
    //     data: {
    //       eventName: eventName,
    //       eventData: eventData,
    //     },
    //     success: function (data) {
    //       console.log('success');
    //     },
    //     error: function (error) {
    //       console.log('error');
    //     },
    //   });
    // }
    $(document).ready(function () {
        sendQueuedEvents();
        if (gtgaEcommerceEnabled())
            trackWooCommerceEvents();
        if (wpGoalTrackerGa.hasOwnProperty('videoSettings') &&
            (wpGoalTrackerGa.videoSettings.gaMediaAudioTracking ||
                wpGoalTrackerGa.videoSettings.gaMediaVideoTracking ||
                wpGoalTrackerGa.videoSettings.gaVimeoVideoTracking ||
                wpGoalTrackerGa.videoSettings.gaYoutubeVideoTracking)) {
            if (wpGoalTrackerGa.videoSettings.gaYoutubeVideoTracking) {
                var tag = document.createElement('script');
                tag.src = '//www.youtube.com/iframe_api';
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                window.onYouTubeIframeAPIReady = function () {
                    youTubeApiIsReady = true;
                    for (var i = 0; i < youTubeApiCallbacks.length; i++) {
                        youTubeApiCallbacks[i]();
                    }
                    youTubeApiCallbacks = [];
                };
                checkForYoutubeVideos();
                checkForElementorLazyLoadedVideos();
            }
            checkForVimeoVideos();
            if (wpGoalTrackerGa.videoSettings.gaMediaVideoTracking) {
                register_media_tracking('video');
            }
            if (wpGoalTrackerGa.videoSettings.gaMediaAudioTracking) {
                register_media_tracking('audio');
            }
        }
    });
    // }
    ////////////////////////////////////////////////////////////////
    ////// Contact Form 7     													 ///////////
    ////////////////////////////////////////////////////////////////
    if (gtgaContactForm7Enabled()) {
        if (wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackFormSubmit) {
            document.addEventListener('wpcf7submit', function (event) {
                var eventData = gtgaGetContactForm7EventData(event);
                trackCustomEvent(_this, 'wpcf7_button_click', eventData);
            }, false);
        }
        if (wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackMailSent) {
            document.addEventListener('wpcf7mailsent', function (event) {
                var eventData = gtgaGetContactForm7EventData(event);
                trackCustomEvent(_this, 'wpcf7_submission', eventData);
            }, false);
        }
        if (wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackMailFailed) {
            document.addEventListener('wpcf7mailfailed', function (event) {
                var eventData = gtgaGetContactForm7EventData(event);
                trackCustomEvent(_this, 'wpcf7_email_failed', eventData);
            }, false);
        }
        if (wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackInvalids) {
            document.addEventListener('wpcf7invalid', function (event) {
                var eventData = gtgaGetContactForm7EventData(event);
                trackCustomEvent(_this, 'wpcf7_invalid', eventData);
            }, false);
        }
        if (wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackSpam) {
            document.addEventListener('wpcf7spam', function (event) {
                var eventData = gtgaGetContactForm7EventData(event);
                trackCustomEvent(_this, 'wpcf7_spam', eventData);
            }, false);
        }
    }
    ////////////////////////////////////////////////////////////////
    /* </fs_premium_only> */
    function returnOriginalProp(self, prop) {
        return prop;
    }
    function getPageName() {
        if ('1' === wpGoalTrackerGa.isFrontPage) {
            return 'Home';
        }
        else {
            if (typeof wpGoalTrackerGa.pageTitle !== 'undefined') {
                return wpGoalTrackerGa.pageTitle;
            }
        }
        return '';
    }
    var trackCustomEvent = typeof trackCustomEventPro === 'function'
        ? trackCustomEventPro
        : trackCustomEventBasic;
    var prepareProps = typeof get_placeholder === 'function'
        ? get_placeholder
        : returnOriginalProp;
    return { isJustHashLink: isJustHashLink };
})(jQuery);
/* <fs_premium_only> */
function getVideoPosition(currentPos, eventsSent) {
    var roundedPos = 0;
    if (currentPos > 10 && currentPos <= 24) {
        if (!eventsSent[10]) {
            roundedPos = 10;
        }
    }
    else if (currentPos >= 25 && currentPos <= 35) {
        if (!eventsSent[25]) {
            roundedPos = 25;
        }
    }
    else if (currentPos >= 50 && currentPos <= 60) {
        if (!eventsSent[50]) {
            roundedPos = 50;
        }
    }
    else if (currentPos >= 75 && currentPos <= 85) {
        if (!eventsSent[75]) {
            roundedPos = 75;
        }
    }
    return roundedPos;
}
function gtgaGetContactForm7EventData(event) {
    var eventTarget = event.target;
    var formTitle = jQuery('#' + eventTarget['wpcf7'].unitTag + ' form').attr('aria-label');
    var eventData = {
        form_id: event.detail.contactFormId,
    };
    if (typeof formTitle !== 'undefined')
        eventData['form_name'] = formTitle;
    return eventData;
}
function gtgaContactForm7Enabled() {
    return (typeof wpGoalTrackerGa.formTrackingSettings !== 'undefined' &&
        wpGoalTrackerGa.formTrackingSettings.contactForm7Settings &&
        (wpGoalTrackerGa.formTrackingSettings.contactForm7Settings
            .trackFormSubmit ||
            wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackInvalids ||
            wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackMailSent ||
            wpGoalTrackerGa.formTrackingSettings.contactForm7Settings
                .trackMailFailed ||
            wpGoalTrackerGa.formTrackingSettings.contactForm7Settings.trackSpam));
}
function gtgaEcommerceEnabled() {
    if (typeof wpGoalTrackerGa.ecommerceTrackingSettings === 'undefined' ||
        typeof wpGoalTrackerGa.ecommerceTrackingSettings.wooCommerceSettings ===
            'undefined')
        return false;
    var wooCommerceSettings = wpGoalTrackerGa.ecommerceTrackingSettings.wooCommerceSettings;
    return (wooCommerceSettings.viewItem ||
        wooCommerceSettings.addToCart ||
        wooCommerceSettings.beginCheckout ||
        wooCommerceSettings.purchase ||
        wooCommerceSettings.viewCart ||
        wooCommerceSettings.addPaymentInfo ||
        wooCommerceSettings.addShippingInfo);
}
/* </fs_premium_only> */
