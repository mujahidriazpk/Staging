(window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[]).push([[68],{121:function(t,e,n){"use strict";n.d(e,"a",(function(){return o}));var c=n(45),s=n(18);const o=t=>Object(c.a)(t)?JSON.parse(t)||{}:Object(s.a)(t)?t:{}},18:function(t,e,n){"use strict";n.d(e,"a",(function(){return c})),n.d(e,"b",(function(){return s}));const c=t=>!(t=>null===t)(t)&&t instanceof Object&&t.constructor===Object;function s(t,e){return c(t)&&e in t}},200:function(t,e,n){"use strict";n.d(e,"a",(function(){return s})),n(103);var c=n(48);const s=()=>c.m>1},251:function(t,e,n){"use strict";n.d(e,"a",(function(){return o}));var c=n(18),s=n(121);const o=t=>{const e=Object(c.a)(t)?t:{},n=Object(s.a)(e.style),o=Object(c.a)(n.typography)?n.typography:{};return{style:{fontSize:e.fontSize?`var(--wp--preset--font-size--${e.fontSize})`:o.fontSize,lineHeight:o.lineHeight,fontWeight:o.fontWeight,textTransform:o.textTransform,fontFamily:e.fontFamily}}}},271:function(t,e,n){"use strict";n.d(e,"a",(function(){return a}));var c=n(114),s=n(200),o=n(18),r=n(121);const a=t=>{if(!Object(s.a)())return{className:"",style:{}};const e=Object(o.a)(t)?t:{},n=Object(r.a)(e.style);return Object(c.__experimentalUseColorProps)({...e,style:n})}},358:function(t,e){},407:function(t,e,n){"use strict";n.r(e);var c=n(0),s=n(1),o=n(4),r=n.n(o),a=n(47),u=n(271),i=n(251),l=n(3),b=n(119);n(358),e.default=Object(b.withProductDataContext)(t=>{const{className:e}=t,{parentClassName:n}=Object(a.useInnerBlockLayoutContext)(),{product:o}=Object(a.useProductDataContext)(),b=Object(u.a)(t),f=Object(i.a)(t);return Object(l.isEmpty)(o.tags)?null:Object(c.createElement)("div",{className:r()(e,b.className,"wc-block-components-product-tag-list",{[n+"__product-tag-list"]:n}),style:{...b.style,...f.style}},Object(s.__)("Tags:","woo-gutenberg-products-block")," ",Object(c.createElement)("ul",null,Object.values(o.tags).map(t=>{let{name:e,link:n,slug:s}=t;return Object(c.createElement)("li",{key:"tag-list-item-"+s},Object(c.createElement)("a",{href:n},e))})))})}}]);