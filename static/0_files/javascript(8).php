/*!
* screenfull
* v4.2.1 - 2019-07-27
* (c) Sindre Sorhus; MIT License
*/
!function(){"use strict";var c="undefined"!=typeof window&&void 0!==window.document?window.document:{},e="undefined"!=typeof module&&module.exports,s="undefined"!=typeof Element&&"ALLOW_KEYBOARD_INPUT"in Element,i=function(){for(var e,n=[["requestFullscreen","exitFullscreen","fullscreenElement","fullscreenEnabled","fullscreenchange","fullscreenerror"],["webkitRequestFullscreen","webkitExitFullscreen","webkitFullscreenElement","webkitFullscreenEnabled","webkitfullscreenchange","webkitfullscreenerror"],["webkitRequestFullScreen","webkitCancelFullScreen","webkitCurrentFullScreenElement","webkitCancelFullScreen","webkitfullscreenchange","webkitfullscreenerror"],["mozRequestFullScreen","mozCancelFullScreen","mozFullScreenElement","mozFullScreenEnabled","mozfullscreenchange","mozfullscreenerror"],["msRequestFullscreen","msExitFullscreen","msFullscreenElement","msFullscreenEnabled","MSFullscreenChange","MSFullscreenError"]],l=0,r=n.length,t={};l<r;l++)if((e=n[l])&&e[1]in c){for(l=0;l<e.length;l++)t[n[0][l]]=e[l];return t}return!1}(),r={change:i.fullscreenchange,error:i.fullscreenerror},n={request:function(u){return new Promise(function(e,n){var l,r=i.requestFullscreen,t=function(){this.off("change",t),e()}.bind(this);this.on("change",t),u=u||c.documentElement,l=/ Version\/5\.1(?:\.\d+)? Safari\//.test(navigator.userAgent)?u[r]():u[r](s?Element.ALLOW_KEYBOARD_INPUT:{}),Promise.resolve(l).catch(n)}.bind(this))},exit:function(){return new Promise(function(e){if(this.isFullscreen){var n=function(){this.off("change",n),e()}.bind(this);c[i.exitFullscreen](),this.on("change",n)}else e()}.bind(this))},toggle:function(e){return this.isFullscreen?this.exit():this.request(e)},onchange:function(e){this.on("change",e)},onerror:function(e){this.on("error",e)},on:function(e,n){var l=r[e];l&&c.addEventListener(l,n,!1)},off:function(e,n){var l=r[e];l&&c.removeEventListener(l,n,!1)},raw:i};i?(Object.defineProperties(n,{isFullscreen:{get:function(){return Boolean(c[i.fullscreenElement])}},element:{enumerable:!0,get:function(){return c[i.fullscreenElement]}},enabled:{enumerable:!0,get:function(){return Boolean(c[i.fullscreenEnabled])}}}),e?(module.exports=n,module.exports.default=n):window.screenfull=n):e?module.exports=!1:window.screenfull=!1}();
var currentFullScreen=!1
function Slider(){this.svgNS='http://www.w3.org/2000/svg'
this.xlinkNS='http://www.w3.org/1999/xlink'
this.images=new Array()
this.slide=0}
Slider.prototype.createItem=function(element,owner,params,replace){owner=(owner)?owner:document.body
result=(element)?document.createElement(element):!1
if(result&&owner)
if(replace)
owner.replaceChild(result,replace)
else owner.appendChild(result)
if(params){if(typeof(params)=='object'){for(var k in params)
if(params.hasOwnProperty(k))
result.setAttribute(k,params[k])}
if(typeof(params)=='string')
result.setAttribute(params,'')}
return result}
Slider.prototype.createSlider=function(element){this.presentation=this.createItem('div',element.parentNode,{'class':'presentation form-group'},element)
this.createViewport()
this.createPanel()
var self=this
window.addEventListener('resize',function(e){self.resizeViewport()},!1)
this.resizeViewport()
screenfull.initialPoint=0
screenfull.finalPoint=0}
Slider.prototype.addTouchEvents=function(){var self=this
document.addEventListener('touchstart',function(e){},!1)}
Slider.prototype.createViewport=function(){this.viewport=this.createItem('div',this.presentation,{'class':'viewport'})
this.updateSlide()
this.fscrEvent(this.viewport,'dblclick')}
Slider.prototype.fscrEvent=function(subj,event){var self=this
subj.addEventListener(event,function(e){if(screenfull.enabled){self.toggleFullScreen()
self.resizeViewport()}},!1)}
Slider.prototype.appendImage=function(base64){var img=new Image()
img.src='data:image/jpg;base64,'+base64
this.images[this.images.length]=img}
Slider.prototype.createPanel=function(){this.panel=this.createItem('div',this.presentation,{'class':'controlpanel'})
this.panel=this.createItem('div',this.panel,{'class':'form-group'})
this.createPrevButton()
this.createNextButton()
this.createInfoText()
this.createFullscreenButton()}
Slider.prototype.onChangeSlide=function(){if(this.info)
this.info.innerHTML=(this.slide/1+1)+' / '+this.images.length}
Slider.prototype.createPrevButton=function(){this.prevButton=document.createElementNS(this.svgNS,"svg")
var path=document.createElementNS(this.svgNS,"use")
path.setAttributeNS(this.xlinkNS,'xlink:href','#prev')
this.prevButton.appendChild(path)
this.panel.appendChild(this.prevButton)
var self=this
this.prevButton.addEventListener('click',function(e){self.prevSlide()},!1)}
Slider.prototype.createInfoText=function(){this.info=this.createItem('div',this.panel,{'class':'info','style':'display: inline; position: absolute; line-height: 30px;'},!1)
this.onChangeSlide()}
Slider.prototype.createNextButton=function(){this.nextButton=document.createElementNS(this.svgNS,"svg")
var path=document.createElementNS(this.svgNS,"use")
path.setAttributeNS(this.xlinkNS,'xlink:href','#next')
this.nextButton.appendChild(path)
this.panel.appendChild(this.nextButton)
var self=this
this.nextButton.addEventListener('click',function(e){self.nextSlide()},!1)}
Slider.prototype.updateSlide=function(){this.viewport.style.backgroundImage="url('"+this.images[this.slide].src+"')"
this.onChangeSlide()}
Slider.prototype.prevSlide=function(){if(this.slide>0){this.slide--
this.updateSlide()}}
Slider.prototype.nextSlide=function(){if(this.slide<this.images.length-1){this.slide++
this.updateSlide()}else{if(screenfull.isFullscreen)
if(screenfull.enabled)
this.toggleFullScreen()}}
Slider.prototype.createFullscreenButton=function(){this.fullscreenButton=document.createElementNS(this.svgNS,"svg")
this.fullscreenButton.setAttribute('class','right')
var path=document.createElementNS(this.svgNS,"use")
path.setAttributeNS(this.xlinkNS,'xlink:href','#full')
this.fullscreenButton.appendChild(path)
this.panel.appendChild(this.fullscreenButton)
this.fscrEvent(this.fullscreenButton,'click')}
Slider.prototype.toggleFullScreen=function(){var fs=!screenfull.isFullscreen
screenfull.toggle(this.presentation)
if(fs){screenfull.currentFullScreen=this
window.addEventListener('keyup',keyPressed,!1)}else{window.removeEventListener('keyup',keyPressed)
screenfull.currentFullScreen=!1}}
Slider.prototype.resizeViewport=function(){if(this.viewport)
if(screenfull.isFullscreen)
this.viewport.style.height=window.outerHeight-this.panel.offsetHeight+'px'
else this.viewport.style.height=Math.round(this.viewport.offsetWidth*0.5625)+'px'}
window.addEventListener('load',function(e){var presentations=document.getElementsByTagName('presentation')
var prs=new Array()
if(presentations.length>0)
for(p in presentations)
if(presentations.hasOwnProperty(p)){presentationApp=new Slider
var presentation=presentations[p]
var slides=presentation.getElementsByTagName('slide')
if(slides.length>0){for(s in slides)
if(slides.hasOwnProperty(s)){var slide=slides[s]
presentationApp.appendImage(slide.getAttribute('data'))}
presentationApp.createSlider(presentation.getElementsByTagName('slides')[0])}}},!1)
document.addEventListener('touchstart',function(){screenfull.initialPoint=event.changedTouches[0]},!1)
document.addEventListener('touchend',swipe,!1)
function keyPressed(e){if(screenfull.currentFullScreen){if((e.keyCode==27)||(!screenfull.isFullscreen)){window.removeEventListener('keyup',keyPressed)
screenfull.currentFullScreen=!1}
if(e.keyCode==37)
screenfull.currentFullScreen.prevSlide()
if(e.keyCode==39)
screenfull.currentFullScreen.nextSlide()}}
function swipe(e){screenfull.finalPoint=e.changedTouches[0]
var xAbs=Math.abs(screenfull.initialPoint.pageX-screenfull.finalPoint.pageX)
var yAbs=Math.abs(screenfull.initialPoint.pageY-screenfull.finalPoint.pageY)
if(xAbs>20||yAbs>20){if(xAbs>yAbs){if(screenfull.finalPoint.pageX<screenfull.initialPoint.pageX){if(screenfull.currentFullScreen)
screenfull.currentFullScreen.nextSlide()}else{if(screenfull.currentFullScreen)
screenfull.currentFullScreen.prevSlide()}}}}