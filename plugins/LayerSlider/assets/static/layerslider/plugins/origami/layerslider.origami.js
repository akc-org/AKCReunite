
/*
	* LayerSlider Add-On: Origami Slide Transition
	*
	* (c) 2011-2025 George Krupa, John Gera & Kreatura Media
	*
	* LayerSlider home:		https://layerslider.com/
	* Licensing:			https://layerslider.com/licensing/
*/



;!function(h){window._layerSlider.plugins.origami=function(m,i,t,s){var f=this;f.pluginData={name:"Origami Slide Transition Add-On for LayerSlider",version:"1.6",requiredLSVersion:"7.5.1",authorName:"Kreatura",releaseDate:"2024. 01. 22."},f.pluginDefaults={opacity:.25,maxTiles:4},f.init=function(){f.extendLayerSlider()},f.extendLayerSlider=function(){m.transitions.slide.origami={start:function(){m.slider.ceilRatio=Math.ceil(m.slider.width/m.slider.height),this.blocksNum=m.slider.ceilRatio>f.pluginDefaults.maxTiles?f.pluginDefaults.maxTiles:m.slider.ceilRatio,this.blocksNum=Math.floor(Math.random()*this.blocksNum)+1,this.addBlocks()},getDirection:function(i){for(var t=this.lastDir;this.lastDir==t;)t=1<this.blocksNum?0===i?["right","top","right","bottom"][Math.floor(4*Math.random())]:i==this.blocksNum-1?["left","top","left","bottom"][Math.floor(4*Math.random())]:["top","bottom"][Math.floor(2*Math.random())]:["left","top","right","bottom"][Math.floor(4*Math.random())];return this.lastDir=t},addBlocks:function(){for(var i=m.slider.width%2==0?m.slider.width:m.slider.width+1,t=i/this.blocksNum%2==0?i/this.blocksNum:i/this.blocksNum-i/this.blocksNum%2,s=m.slider.height%2==0?m.slider.height:m.slider.height+1,o=0,a=0;a<this.blocksNum;a++){var e=this.blocksNum-Math.abs(Math.floor(this.blocksNum/2)-a)-Math.floor(this.blocksNum/2),r=this.getDirection(a),n=t;i/this.blocksNum%2!=0&&a%2==0&&(i-t*this.blocksNum)/2<this.blocksNum&&(n+=2),a===this.blocksNum-1&&o+n!==i&&(n=i-o),a===this.blocksNum-1&&i!==m.slider.width&&--o;var l=m.transitions.slide.origami.createBlock("ls-origami-"+r,n,s,o,0).data({direction:r});l.css({zIndex:e}),this.appendTiles(l,o,r,a),o+=n}m.transitions.slide.start()},createBlock:function(i,t,s,o,a){return h("<div>").addClass("ls-origami-block "+i).css({width:t,height:s,left:o,top:a}).appendTo(m.transitions.slide.$wrapper)},appendTiles:function(c,p,i,t){var s;switch(m.transitions.slide.$wrapper.prependTo(m.slider.$layersWrapper),i){case"left":case"right":s={width:c.width()/2};break;case"top":case"bottom":s={height:c.height()/2}}var o=h("<div>").css(s).addClass("ls-origami-tile ls-origami-cur").appendTo(c),a=h("<div>").css(s).addClass("ls-origami-tile ls-origami-cur").appendTo(o),e=h("<div>").css(s).addClass("ls-origami-tile ls-origami-next").appendTo(a),r=h("<div>").css(s).addClass("ls-origami-tile ls-origami-next").appendTo(e);c.find(".ls-origami-tile").each(function(){var i=h(this).hasClass("ls-origami-next")?"next":"current",t=h("<div>").addClass("ls-origami-image-holder").appendTo(h(this));if(m.slides[i].data.$background){var s,o,a=h(this).parent();switch(c.data("direction")){case"left":switch(i){case"current":for(s=h(this).position().left;!a.is(".ls-origami-block");)s+=a.position().left,a=a.parent();break;case"next":for(s=0;!a.is(".ls-origami-cur");)s+=a.position().left,a=a.parent()}s=-p-s;break;case"right":switch(i){case"current":for(s=-h(this).position().left;!a.is(".ls-origami-block");)s-=a.position().left,a=a.parent();break;case"next":for(s=h(this).position().left;!a.is(".ls-origami-cur");)s-=a.position().left,a=a.parent()}s=-p+s;break;case"top":switch(i){case"current":for(o=-h(this).position().top;!a.is(".ls-origami-block");)o-=a.position().top,a=a.parent();break;case"next":for(o=0;!a.is(".ls-origami-cur");)o-=a.position().top,a=a.parent()}s=-p;break;case"bottom":switch(i){case"current":for(o=-h(this).position().top;!a.is(".ls-origami-block");)o-=a.position().top,a=a.parent();break;case"next":for(o=h(this).position().top;!a.is(".ls-origami-cur");)o-=a.position().top,a=a.parent()}s=-p}var e=m.o.playByScroll&&"up"===m.device.scroll.direction?"to":"from",r="current"==i?m.transitions.curSlide:m.transitions.nextSlide,n=r.data.$background.data(m.defaults.init.dataKey),l=n.kenBurns[e],e=!!r.data.$background&&m.functions.getURL(r.data.$background),d=h("<img>").appendTo(t).attr("src",e).css({width:n.responsive.width,height:n.responsive.height,"-webkit-filter":n.responsive.filter,filter:n.responsive.filter,marginLeft:s,marginTop:o,outline:"1px solid transparent"});switch(i){case"current":d.css({"-ms-transform":"translateX("+n.responsive.x+"px) translateY("+n.responsive.y+"px)"+n.responsive.kbRotation+n.responsive.kbScale,"-webkit-transform":"translateX("+n.responsive.x+"px) translateY("+n.responsive.y+"px)"+n.responsive.kbRotation+n.responsive.kbScale,transform:"translateX("+n.responsive.x+"px) translateY("+n.responsive.y+"px)"+n.responsive.kbRotation+n.responsive.kbScale});break;case"next":d.css({"-ms-transform":"translateX("+n.responsive.x+"px) translateY("+n.responsive.y+"px) rotate("+l.rotation+"deg) scale("+l.scale+")","-webkit-transform":"translateX("+n.responsive.x+"px) translateY("+n.responsive.y+"px) rotate("+l.rotation+"deg) scale("+l.scale+")",transform:"translateX("+n.responsive.x+"px) translateY("+n.responsive.y+"px) rotate("+l.rotation+"deg) scale("+l.scale+")"})}"transparent"===r.data.backgroundColor||r.data.$backgroundVideo.length||t.css("background-color",r.data.backgroundColor),m.slider.$slideBGColorWrapper.css("background-color","transparent"),h("<div>").addClass("ls-light").appendTo(t)}}),this.setTransition(c,i,o,a,e,r,t)},setTransition:function(i,t,s,o,a,e,r){i.find(".ls-light").addClass("ls-black");var n=s.find("> .ls-origami-image-holder > div"),l=o.find("> .ls-origami-image-holder > div"),d=a.find("> .ls-origami-image-holder > div"),c=e.find("> .ls-origami-image-holder > div"),p=m.gsap.Cubic.easeInOut,h=f.pluginDefaults.opacity,i=1.5*h;switch(t){case"left":m.transitions._slideTransition.to(s[0],2,{ease:p,rotationY:89},0).to(o[0],2,{ease:p,rotationY:-180},0).fromTo(a[0],2,{rotationY:130},{ease:p,rotationY:91},0).fromTo(e[0],2,{rotationY:90},{ease:p,rotationY:0},1);break;case"right":m.transitions._slideTransition.to(s[0],2,{ease:p,rotationY:-89},0).to(o[0],2,{ease:p,rotationY:180},0).fromTo(a[0],2,{rotationY:-130},{ease:p,rotationY:-91},0).fromTo(e[0],2,{rotationY:-90},{ease:p,rotationY:0},1);break;case"top":m.transitions._slideTransition.to(s[0],2,{ease:p,rotationX:-89},0).to(o[0],2,{ease:p,rotationX:180},0).fromTo(a[0],2,{rotationX:-130},{ease:p,rotationX:-91},0).fromTo(e[0],2,{rotationX:-90},{ease:p,rotationX:0},1);break;case"bottom":m.transitions._slideTransition.to(s[0],2,{ease:p,rotationX:89},0).to(o[0],2,{ease:p,rotationX:-180},0).fromTo(a[0],2,{rotationX:130},{ease:p,rotationX:91},0).fromTo(e[0],2,{rotationX:90},{ease:p,rotationX:0},1)}n[0]&&m.transitions._slideTransition.fromTo(n[0],2,{opacity:0},{ease:p,opacity:1-i},0),l[0]&&m.transitions._slideTransition.fromTo(l[0],2,{opacity:0},{ease:p,opacity:1-h},0),d[0]&&m.transitions._slideTransition.fromTo(d[0],2,{opacity:1-i},{ease:p,opacity:0},0),c[0]&&m.transitions._slideTransition.fromTo(c[0],2,{opacity:1-i},{ease:p,opacity:0},1)}},m.transitions.slide.select.slideTransitionType=function(){!m.slides.next.data.transitionorigami||!m.browser.supports3D||(m.transitions.nextSlide.data.transition3d||m.transitions.nextSlide.data.transition2d)&&Math.floor(2*Math.random())+1===1?m.transitions.slide.normal.select.transitionType():m.transitions.slide.origami.start()}}}}(jQuery);