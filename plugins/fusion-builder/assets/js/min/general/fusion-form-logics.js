window.fusionFormLogics={onReady:function(){jQuery(".fusion-form input, .fusion-form select, .fusion-form textarea").on("change keyup",function(){void 0!==jQuery(this).attr("name")&&""!==jQuery(this).attr("name")&&window.fusionFormLogics.formLogics(jQuery(this))})},formLogics:function(o=""){""===o?jQuery(".fusion-form.fusion-form-builder").each(function(){var o=jQuery(this).data("form-id");window.fusionFormLogics.applyLogics(o)}):window.fusionFormLogics.applyLogics(o.closest(".fusion-form-field").data("form-id"),o.attr("name"))},applyLogics:function(o,i=""){var e=jQuery(".fusion-form-form-wrapper.fusion-form-"+o).data("config"),n=e.field_logics,r={};void 0!==e.field_logics&&(""!==i&&(jQuery.each(n,function(o,e){-1===i.indexOf(o)&&-1===e.indexOf(i)||""===e||(r[o]=e)}),0===Object.keys(r).length)||(n=Object.keys(r).length?r:n,jQuery.each(n,function(i,e){window.fusionFormLogics.checkFieldLogic(i,e,o)})))},checkFieldLogic:function(o,i,e){var n=""!==i?JSON.parse(i):[],r=jQuery(".fusion-form-"+e),t=r.find('[name="'+o+'"]'),a=r.find(".data-"+o),s=r.find('[data-form-element-name="'+o+'"]'),f=!1,u=[];t=t.length?t:r.find('[name="'+o+'[]"]'),jQuery.each(n,function(o,i){var e,n=[],t=void 0!==i.operator?i.operator:"",a=void 0!==i.comparison?i.comparison:"",s=void 0!==i.field?i.field:"",f=void 0!==i.value?i.value:"";e=window.fusionFormLogics.getFieldValue(s,r),n.push(t),n.push("false"!==e&&window.fusionFormLogics.isMatch(e,f,a)),u.push(n)}),u.length&&(f=window.fusionFormLogics.MatchConditions(u),window.fusionFormLogics.toggleField(f,t),window.fusionFormLogics.toggleNotice(f,a),window.fusionFormLogics.toggleElement(f,s))},toggleField:function(o,i){var e=i.attr("aria-required");o?(i.closest(".fusion-form-field").removeClass("fusion-form-field-hidden"),void 0!==e&&"true"===e&&i.attr("required",!0)):i.closest(".fusion-form-field").addClass("fusion-form-field-hidden")},toggleNotice:function(o,i){o?i.removeClass("fusion-form-notice-hidden"):i.addClass("fusion-form-notice-hidden")},toggleElement:function(o,i){o?(i.removeClass("fusion-form-field-hidden"),i.find("input, textarea, select").filter("[data-required]").attr("required",!0).removeAttr("data-required")):i.addClass("fusion-form-field-hidden")},getFieldValue:function(o,i){var e=i.find('[name="'+o+'"]'),n=-1!==jQuery.inArray(e.attr("type"),["checkbox","radio"]);return!e.closest(".fusion-form-field-hidden").hasClass("fusion-form-field-hidden")&&(n?window.fusionFormLogics.getArrayTypeValue(e,i):e.val())},getArrayTypeValue:function(o,i){var e=[];return"radio"===o.attr("type")?i.find('input[name="'+o.attr("name")+'"]:checked').val():(jQuery.each(i.find('input[name="'+o.attr("name")+'"]:checked'),function(){e.push(jQuery(this).val())}),e.join(" | "))},isMatch:function(o,i,e){switch(o=o?o.toLowerCase():"",i=i?i.toLowerCase():"",e){case"equal":return o===i;case"not-equal":return o!==i;case"greater-than":return parseFloat(o)>parseFloat(i);case"less-than":return parseFloat(o)<parseFloat(i);case"contains":return 0<=o.indexOf(i)}},MatchConditions:function(o){var i,e=null;if(-1==o.toString().indexOf("and")){for(i=0;i<o.length;i++)e=(e=null===e?o[i][1]:e)||o[i][1];return e}if(-1==o.toString().indexOf("or")){for(i=0;i<o.length;i++)e=(e=null===e?o[i][1]:e)&&o[i][1];return e}return window.fusionFormLogics.matchMixedConditions(o)},matchMixedConditions:function(o){var i,e=[],n="",r=0,t=0,a=o.length,s=[],f="",u="",d="",c="",l="";for(i=0;i<a;i++)void 0===e[r]&&(e[r]=[]),""===n||n==o[i][0]?(e[r][t]=o[i][1],t++,e[r][t]=o[i][0],t++,n=o[i][0]):(e[r][t]=o[i][1],t++,e[r][t]=o[i][0],r++,t=0,n="");if(jQuery.each(e,function(o,e){if(3>(a=e.length))return s.push(e[0]),void s.push(e[1]);for(i=0;i<a-1;i++)""===u?(c=e[i],l=e[i+2],d=e[i+1],u="or"===d?c||l:c&&l,i+=2):(c=u,l=e[i+1],d=e[i],u="or"===d?c||l:c&&l,i++),!0!==u&&(u=!1);f=e,s.push(u),s.push(f[a-1]),u=""}),u="",d="",c="",l="",3>(a=s.length))return s[0];for(i=0;i<a-1;i++)""===u?(c=s[i],l=s[i+2],d=s[i+1],i+=2,u="or"===d?c||l:c&&l):(c=u,l=s[i+1],d=s[i],u="or"===d?c||l:c&&l,i++),!0!==u&&(u=!1);return u}},function(o){o(document).ready(function(){window.fusionFormLogics.onReady(),window.fusionFormLogics.formLogics()})}(jQuery);