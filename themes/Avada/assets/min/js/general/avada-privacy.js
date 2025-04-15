function fusionGetCookieValue(){var e=fusionGetConsentValues("undefined"!=typeof avadaPrivacyVars?avadaPrivacyVars.name:"");return"object"!=typeof e&&(e=[]),e}function fusionGetConsent(e){var a=fusionGetConsentValues("undefined"!=typeof avadaPrivacyVars?avadaPrivacyVars.name:""),i="undefined"!=typeof avadaPrivacyVars?avadaPrivacyVars.types:[];return"undefined"==typeof avadaPrivacyVars||(-1===jQuery.inArray(e,i)||("object"!=typeof a&&(a=[]),-1!==jQuery.inArray(e,a)))}function fusionReplacePlaceholders(e){jQuery('[data-privacy-type="'+e+'"]').each(function(){fusionReplacePlaceholder(jQuery(this))}),jQuery(window).trigger("awb-privacy-placeholders-removed")}function fusionReplacePlaceholder(e){var a,i,n,r;e.is("iframe")||e.is("img")?(e.attr("src",e.attr("data-privacy-src")),e.removeClass("fusion-hidden"),"gmaps"===e.attr("data-privacy-type")&&e.parents(".fusion-maps-static-type").removeClass("fusion-hidden")):e.is("priv-fac-lite-youtube")||e.is("priv-fac-lite-vimeo")?(e.removeClass("fusion-hidden"),n=(n=e[0].outerHTML).replace(/priv-fac-/g,""),e[0].outerHTML=n):e.attr("data-privacy-video")&&e.is("noscript")?(e.after(e.text()),e.remove(),"undefined"!=typeof wp&&void 0!==wp.mediaelement&&wp.mediaelement.initialize()):e.attr("data-privacy-script")&&(e.is("span")||e.is("noscript"))&&(a=document.createElement("script"),i=void 0!==e.attr("data-privacy-src")&&e.attr("data-privacy-src"),r=""!==e.text()&&e.text(),i&&(a.src=i),r&&(a.innerHTML=r.replace(/data-privacy-src=/g,"src=")),r&&-1!==r.indexOf("google.maps")||i&&-1!==i.indexOf("infobox_packed")?fusionMapInsert(a):document.body.appendChild(a),e.remove())}function fusionGetConsentValues(e){var a=("; "+decodeURIComponent(document.cookie)).split("; "+e+"=");return 2===a.length&&a.pop().split(";").shift().split(",")}function fusionMapInsert(e){if("undefined"!=typeof google&&(!jQuery('[src*="infobox_packed"], [data-privacy-src*="infobox_packed"]').length||"undefined"!=typeof InfoBox))return document.body.appendChild(e),void jQuery(".fusion-google-map").each(function(){jQuery(this).removeClass("fusion-hidden"),"function"==typeof window["fusion_run_map_"+jQuery(this).attr("id")]&&window["fusion_run_map_"+jQuery(this).attr("id")]()});setTimeout(function(){fusionMapInsert(e)},1e3)}function fusionSaveCookieValues(e,a){var i,n="undefined"!=typeof avadaPrivacyVars?avadaPrivacyVars.name:"",r=fusionGetCookieValue(),o="undefined"!=typeof avadaPrivacyVars?avadaPrivacyVars.path:"/",t="undefined"!=typeof avadaPrivacyVars?avadaPrivacyVars.days:"30",u=new Date;a?r.push(e):r=e,u.setTime(u.getTime()+24*t*60*60*1e3),i="expires="+u.toUTCString(),document.cookie=n+"="+r.join(",")+";"+i+";path="+o}function fusionSliderVideoInit(e,a,i){return(e||a)&&jQuery(".tfs-slider").each(function(){var n;(e&&jQuery(this).find('[data-privacy-type="vimeo"]').length||a&&jQuery(this).find('[data-privacy-type="youtube"]').length)&&void 0!==(n=jQuery(this).data("flexslider"))&&(n.resize(),!i&&a&&"function"==typeof registerYoutubePlayers&&jQuery(this).find('[data-privacy-type="youtube"]').length&&(registerYoutubePlayers(!0),loadYoutubeIframeAPI(),i=!0),"function"!=typeof playVideoAndPauseOthers||a&&"function"==typeof registerYoutubePlayers&&jQuery(this).find('[data-privacy-type="youtube"]').length||playVideoAndPauseOthers(n))}),i}function fusionVideoApiInit(e,a,i){e&&"function"==typeof fusionInitVimeoPlayers&&fusionInitVimeoPlayers(),a&&"function"==typeof onYouTubeIframeAPIReady&&!i&&(registerYoutubePlayers(),loadYoutubeIframeAPI())}function fusionPrivacyBar(){var e=fusionGetCookieValue(),a=[],i=jQuery(".fusion-privacy-bar-acceptance"),n=i.data("alt-text"),r=i.data("orig-text");jQuery.each(e,function(e,a){fusionReplacePlaceholders(a),jQuery(".fusion-privacy-element #"+a+", #bar-"+a).prop("checked",!0),jQuery('.fusion-privacy-placeholder[data-privacy-type="'+a+'"]').remove()}),jQuery(".fusion-privacy-placeholder").each(function(){var e,a=jQuery(this),i=a.parent(),n=a.prev(),r=a.outerWidth(),o=a.outerHeight();i.width(),i.height();!n.is("iframe")||i.hasClass("fusion-background-video-wrapper")||i.hasClass("fluid-width-video-wrapper")||(e=-1!==a.css("width").indexOf("%")?o+"px":o/r*100+"%",a.wrap('<div class="fluid-width-video-wrapper" style="padding-top:'+e+'" />'),a.parent().append(n))}),jQuery(".fusion-privacy-consent").on("click",function(e){var a=jQuery(this).attr("data-privacy-type"),i=fusionGetCookieValue(),n="vimeo"===a,r="youtube"===a,o=!1;-1===jQuery.inArray(a,i)&&fusionSaveCookieValues(a,!0),e.preventDefault(),fusionReplacePlaceholders(a),jQuery(".fusion-privacy-element #"+a+", #bar-"+a).prop("checked",!0),fusionVideoApiInit(n,r,o=fusionSliderVideoInit(n,r,o)),jQuery('.fusion-privacy-placeholder[data-privacy-type="'+a+'"]').remove()}),-1===jQuery.inArray("consent",e)&&jQuery(".fusion-privacy-bar").css({display:"block"}),jQuery(".fusion-privacy-bar-learn-more").on("click",function(e){var a=jQuery(this).parents(".fusion-privacy-bar");e.preventDefault(),a.find(".fusion-privacy-bar-full").slideToggle(300),a.toggleClass("fusion-privacy-bar-open"),jQuery(this).find(".awb-icon-angle-up").length?jQuery(this).find(".awb-icon-angle-up").removeClass("awb-icon-angle-up").addClass("awb-icon-angle-down"):jQuery(this).find(".awb-icon-angle-down").length&&jQuery(this).find(".awb-icon-angle-down").removeClass("awb-icon-angle-down").addClass("awb-icon-angle-up")}),jQuery(".fusion-privacy-bar-acceptance").on("click",function(e){var a=jQuery(this).parents(".fusion-privacy-bar"),n=a.find('input[type="checkbox"]'),r=["consent"],o=!1,t=!1,u=!1,s="undefined"!=typeof avadaPrivacyVars&&1==avadaPrivacyVars.button,c="undefined"!=typeof avadaPrivacyVars?avadaPrivacyVars.defaults:[];e.preventDefault(),a.find(".fusion-privacy-bar-full").is(":visible")||i.hasClass("fusion-privacy-update")||s?(jQuery('.fusion-privacy-element input[type="checkbox"]').prop("checked",!1),n.length?jQuery(n).each(function(){var e=jQuery(this).val();jQuery(this).is(":checked")&&-1!==jQuery(this).attr("name").indexOf("consents")&&(fusionReplacePlaceholders(e),jQuery(".fusion-privacy-element #"+e).prop("checked",!0),jQuery('.fusion-privacy-placeholder[data-privacy-type="'+e+'"]').remove(),r.push(e),"youtube"===e&&(o=!0),"vimeo"===e&&(t=!0))}):s&&c.length&&jQuery.each(c,function(e,a){fusionReplacePlaceholders(a),jQuery(".fusion-privacy-element #"+a).prop("checked",!0),jQuery('.fusion-privacy-placeholder[data-privacy-type="'+a+'"]').remove(),r.push(a),"youtube"===a&&(o=!0),"vimeo"===a&&(t=!0)}),fusionSaveCookieValues(r,!1)):fusionSaveCookieValues("consent",!0),u=fusionSliderVideoInit(t,o,u),fusionVideoApiInit(t,o,u),a.hide()}),jQuery(".fusion-privacy-bar-reject").on("click",function(e){e.preventDefault(),fusionSaveCookieValues("consent",!0),jQuery(this).parents(".fusion-privacy-bar").hide(),location.reload()}),jQuery('.fusion-privacy-bar-full .fusion-privacy-choices input[type="checkbox"]').on("change",function(e){var o=jQuery(this).val();-1===jQuery.inArray(o,a)?a.push(o):a.splice(a.indexOf(o),1),0!==a.length?(i.text(n),i.addClass("fusion-privacy-update")):(i.text(r),i.removeClass("fusion-privacy-update"))})}jQuery(document).ready(function(){fusionPrivacyBar()});