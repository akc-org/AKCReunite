
/*
	* LayerSlider Add-On: Timeline
	*
	* (c) 2011-2025 George Krupa, John Gera & Kreatura Media
	*
	* LayerSlider home:		https://layerslider.com/
	* Licensing:			https://layerslider.com/licensing/
*/



;!function(T){window._layerSlider.plugins.timeline=function(f,t,g,e){var v=this;v.pluginData={name:"Timeline Add-On for LayerSlider",version:"1.5",requiredLSVersion:"7.0.0",authorName:"Kreatura",releaseDate:"2021. 11. 19."},v.pluginDefaults={eventNamespace:"TL",showCurrentTime:!0,showLayersInfo:!0,showLayersProperties:!1},v.init=function(){this.applySettings(),this.createMarkup(),this.createEvents()},v.applySettings=function(){this.settings=T.extend(!0,{},this.pluginDefaults,e)},v.createMarkup=function(){var e=T('[data-timeline-for="'+t.attr("id")+'"]');this.removeElement=!e.length,this.$timelineElement=e.length?e:T("<div>").css({maxWidth:t.css("max-width")}).insertAfter(t),this.$strechedElement=T("<div>").addClass("ls-timeline-streched").appendTo(this.$timelineElement),this.$infoElement=T("<div>").addClass("ls-timeline-layer-info").appendTo(this.$timelineElement),this.$layerTweensWrapper=T("<div>").addClass("ls-layer-tweens-wrapper").appendTo(this.$strechedElement),this.$timingsWrapper=T("<div>").addClass("ls-timings-wrapper").appendTo(this.$strechedElement),this.$timelineElement.addClass("ls-slide-timeline "+(this.settings.showLayersInfo?"ls-show-layer-info":"")),this.$strechedElement.append(T('<div data-slidebar-for="'+g+'"></div>')),this.settings.showCurrentTime&&(this.$currentTimeElement=T("<div>").addClass("ls-current-time")),this.$legendWrapper=T('<div class="ls-timeline-legend"><span class="ls-tl-leg">legend</span><span class="ls-tl-leg-delay">delay</span><span class="ls-tl-leg-in">in</span><span class="ls-tl-leg-out">out</span><span class="ls-tl-leg-textin">text in</span><span class="ls-tl-leg-textout">text out</span><span class="ls-tl-leg-loop">loop / middle</span><span class="ls-tl-leg-static">static</span></div>').appendTo(this.$timelineElement)},v.round=function(e,t){return t=t?t*=10:1e3,Math.round(e*t)/t},v.createEvents=function(){t.on("sliderDidLoad.layerSlider",function(e,t){v.settings.showCurrentTime&&v.$strechedElement.find(".ls-slidebar-slider").append(v.$currentTimeElement)}).on("slideTimelineDidCreate.layerSlider",function(e,t){v.slideTimelineDuration=t.slideTimelineDuration,v.slideTimelineDurationRatio=t.slideTimeline.duration()<t.slideTimelineDuration?1:v.slideTimelineDuration/t.slideTimeline.duration();var i,s=t.layersOnSlideTimeline.filter(":not( .ls-bg )"),n=s.length,l=v.slideTimelineDuration/100;v.$layerTweensWrapper.empty(),v.$timingsWrapper.empty(),v.$infoElement.empty();for(var a=0;a<Math.floor(v.slideTimelineDuration)+1&&(i=a*(100/v.slideTimelineDuration),T('<div class="ls-timeline-seconds'+(99<=(i=100<i?100:i)?" ls-timeline-seconds-last":"")+'"><div class="ls-timeline-sec">'+a+"s</div></div>").css({left:i+"%"}).appendTo(v.$timingsWrapper),!(100<=i));a++);for(var r=1;r<10*v.slideTimelineDuration&&(i=100<(i=r*(100/(10*v.slideTimelineDuration)))?100:i,r%10!=0&&T('<div class="ls-timeline-dsecond"></div>').css({left:i+"%"}).appendTo(v.$timingsWrapper),!(100<=i));r++);for(var d=0;d<n;d++){var o,p,m,u="",c=(m=s.eq(d)).data(f.defaults.init.dataKey),h=T("<div>").addClass("ls-layer-tweens").data("lsTweensOfLayer",m).prependTo(v.$layerTweensWrapper),y=T("<div>").addClass("ls-layer-tweens-inner").appendTo(h);v.settings.showLayersInfo&&(h=T("<div>").data("ls",{$layer:m,layerData:c,$outerWrapper:c.elements.$outerWrapper}).addClass("ls-layer-info").prependTo(v.$infoElement).on("mouseenter."+v.settings.eventNamespace+g,function(){T(this).data("ls").$outerWrapper.attr("id","ls-wrapper-highlighted")}).on("mouseleave."+v.settings.eventNamespace+g,function(){T(this).data("ls").$outerWrapper.removeAttr("id")}),m.is("img")&&h.append(T('<div><a href="'+m.attr("src")+'" target="_blank"></a></div>').css({backgroundImage:"url("+m.attr("src")+")"})),p=m.children().first().length&&m.children().first().is("iframe, video, audio")?"MEDIA LAYER":m[0].innerText,v.settings.showLayersProperties&&(u+="<tr><td>Type & Content</td><td>"+m[0].tagName+(p?" | "+p:"")+"</td></tr>",u+="<tr><td>Original styles</td><td>"+c.original.styles.replace(/:/g,": ").replace(/;/g,"; ")+"</td></tr>",u+="<tr><td>Original data-ls</td><td>"+c.original.dataLS.replace(/:/g,": ").replace(/;/g,"; ")+"</td></tr>",m[0].src&&(u+="<tr><td>Image URL</td><td>"+m[0].src+"</td></tr>"),o='<div class="ls-layer-properties"><table><thead><tr><th colspan="2">Layer Properties</th></tr></thead><tbody>'+u+"</tbody></table></div>"),h.append("<h1>"+(o||"")+m[0].tagName+"<span>"+p+"</span></h1> ")),c.is.static&&f.slides.next.index!==c.settings.slideIn||(T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-transition-in").css({left:v.round(c.timeline.transitioninstart)/l+"%",width:v.round(c.timeline.transitioninend-c.timeline.transitioninstart)/l+"%"}),0<c.timeline.transitioninstart&&T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-delay-in").css({left:0,width:v.round(c.timeline.transitioninstart)/l+"%"})),T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-text-in").css({left:v.round(c.timeline.textinstart)/l+"%",width:v.round(c.timeline.textinend-c.timeline.textinstart)/l+"%"}),p=-1===c.loop.count?v.slideTimelineDuration:c.timeline.loopend,T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-loop").css({left:v.round(c.timeline.loopstart)/l+"%",width:v.round(p-c.timeline.loopstart)/l+"%"}),T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-text-out").css({left:v.round(1e3*c.timeline.textoutstart)/1e3/l+"%",width:v.round(1e3*(c.timeline.textoutend-c.timeline.textoutstart))/1e3/l+"%"}),f.slides.next.index===c.settings.slideOut&&T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-transition-out").css({left:v.round(c.timeline.transitionoutstart)/l+"%",width:v.round(c.timeline.transitionoutend-c.timeline.transitionoutstart)/l+"%"}),c.is.static?f.slides.next.index===c.settings.slideOut?(p="slidechangeonly"===c.out.startAt?"static":"showuntil",T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-"+p).css({left:0,width:"100%"===c.timeline.staticto?c.timeline.staticto:v.round(c.timeline.transitionoutstart)/l+"%"})):T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-static").css({left:v.round(c.timeline.staticfrom)/l+"%",width:"100%"}):T("<div>").appendTo(y).addClass("ls-layer-tween ls-layer-showuntil").css({left:v.round(c.timeline.transitioninend)/l+"%",width:v.round(c.timeline.transitionoutstart-c.timeline.transitioninend)/l+"%"})}0===n&&T('<div class="ls-layer-info no-layers"><h1>No layers found</h1></div>').prependTo(v.$infoElement)}).on("slideTimelineDidUpdate.layerSlider",function(e,t){v.settings.showCurrentTime&&((t=parseInt(v.slideTimelineDuration/v.slideTimelineDurationRatio*t.progress()*1e3)/1e3)!=t&&(t=0),v.$currentTimeElement.text(t.toFixed(3)))}).on("sliderDidDestroy.layerSlider",function(){v.api.destroy()})},v.api={destroy:function(){T(window).add("body").add(t).add(t.find("*")).add(v.$timelineElement).add(v.$timelineElement.find("*")).off("."+v.settings.eventNamespace+g),this.removeElement?v.$timelineElement.remove():v.$timelineElement.empty()}}}}(jQuery);