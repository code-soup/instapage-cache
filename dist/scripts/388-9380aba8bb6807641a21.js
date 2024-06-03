(self.webpackChunk=self.webpackChunk||[]).push([[388],{511:(e,t,n)=>{"use strict";n.d(t,{A:()=>c});var r=n(568),a=n(294);const s=({path:e,url:t,...n},a)=>({...n,url:t&&(0,r.F)(t,a),path:e&&(0,r.F)(e,a)}),o=e=>e.json?e.json():Promise.reject(e),i=e=>{const{next:t}=(e=>{if(!e)return{};const t=e.match(/<([^>]+)>; rel="next"/);return t?{next:t[1]}:{}})(e.headers.get("link"));return t},c=async(e,t)=>{if(!1===e.parse)return t(e);if(!(e=>{const t=!!e.path&&-1!==e.path.indexOf("per_page=-1"),n=!!e.url&&-1!==e.url.indexOf("per_page=-1");return t||n})(e))return t(e);const n=await(0,a.A)({...s(e,{per_page:100}),parse:!1}),r=await o(n);if(!Array.isArray(r))return r;let c=i(n);if(!c)return r;let l=[].concat(r);for(;c;){const t=await(0,a.A)({...e,path:void 0,url:c,parse:!1}),n=await o(t);l=l.concat(n),c=i(t)}return l}},958:(e,t,n)=>{"use strict";n.d(t,{A:()=>s});const r=new Set(["PATCH","PUT","DELETE"]),a="GET",s=(e,t)=>{const{method:n=a}=e;return r.has(n.toUpperCase())&&(e={...e,headers:{...e.headers,"X-HTTP-Method-Override":n,"Content-Type":"application/json"},method:"POST"}),t(e)}},560:(e,t,n)=>{"use strict";n.d(t,{A:()=>s});var r=n(43),a=n(641);const s=(e,t)=>{if(!function(e){const t=!!e.method&&"POST"===e.method;return(!!e.path&&-1!==e.path.indexOf("/wp/v2/media")||!!e.url&&-1!==e.url.indexOf("/wp/v2/media"))&&t}(e))return t(e);let n=0;const s=e=>(n++,t({path:`/wp/v2/media/${e}/post-process`,method:"POST",data:{action:"create-image-subsizes"},parse:!1}).catch((()=>n<5?s(e):(t({path:`/wp/v2/media/${e}?force=true`,method:"DELETE"}),Promise.reject()))));return t({...e,parse:!1}).catch((t=>{const n=t.headers.get("x-wp-upload-attachment-id");return t.status>=500&&t.status<600&&n?s(n).catch((()=>!1!==e.parse?Promise.reject({code:"post_process",message:(0,r.__)("Media upload failed. If this is a photo or a large image, please scale it down and try again.")}):Promise.reject(t))):(0,a.J)(t,e.parse)})).then((t=>(0,a.f)(t,e.parse)))}},729:(e,t,n)=>{"use strict";n.d(t,{A:()=>r});const r=(e,t)=>{let n,r,a=e.path;return"string"==typeof e.namespace&&"string"==typeof e.endpoint&&(n=e.namespace.replace(/^\/|\/$/g,""),r=e.endpoint.replace(/^\//,""),a=r?n+"/"+r:n),delete e.namespace,delete e.endpoint,t({...e,path:a})}},683:(e,t,n)=>{"use strict";n.d(t,{A:()=>r});const r=function(e){const t=(e,n)=>{const{headers:r={}}=e;for(const a in r)if("x-wp-nonce"===a.toLowerCase()&&r[a]===t.nonce)return n(e);return n({...e,headers:{...r,"X-WP-Nonce":t.nonce}})};return t.nonce=e,t}},296:(e,t,n)=>{"use strict";function r(e){const t=e.split("?"),n=t[1],r=t[0];return n?r+"?"+n.split("&").map((e=>e.split("="))).map((e=>e.map(decodeURIComponent))).sort(((e,t)=>e[0].localeCompare(t[0]))).map((e=>e.map(encodeURIComponent))).map((e=>e.join("="))).join("&"):r}n.d(t,{A:()=>i});var a=n(438),s=n(568);function o(e,t){return Promise.resolve(t?e.body:new window.Response(JSON.stringify(e.body),{status:200,statusText:"OK",headers:e.headers}))}const i=function(e){const t=Object.fromEntries(Object.entries(e).map((([e,t])=>[r(e),t])));return(e,n)=>{const{parse:i=!0}=e;let c=e.path;if(!c&&e.url){const{rest_route:t,...n}=(0,a.u)(e.url);"string"==typeof t&&(c=(0,s.F)(t,n))}if("string"!=typeof c)return n(e);const l=e.method||"GET",u=r(c);if("GET"===l&&t[u]){const e=t[u];return delete t[u],o(e,!!i)}if("OPTIONS"===l&&t[l]&&t[l][u]){const e=t[l][u];return delete t[l][u],o(e,!!i)}return n(e)}}},878:(e,t,n)=>{"use strict";n.d(t,{A:()=>a});var r=n(729);const a=e=>(t,n)=>(0,r.A)(t,(t=>{let r,a=t.url,s=t.path;return"string"==typeof s&&(r=e,-1!==e.indexOf("?")&&(s=s.replace("?","&")),s=s.replace(/^\//,""),"string"==typeof r&&-1!==r.indexOf("?")&&(s=s.replace("?","&")),a=r+s),n({...t,url:a})}))},110:(e,t,n)=>{"use strict";n.d(t,{A:()=>c});var r=n(286),a=n(568),s=n(438),o=n(873);function i(e,...t){const n=e.indexOf("?");if(-1===n)return e;const r=(0,s.u)(e),a=e.substr(0,n);t.forEach((e=>delete r[e]));const i=(0,o.G)(r);return i?a+"?"+i:a}const c=e=>(t,n)=>{if("string"==typeof t.url){const n=(0,r.d)(t.url,"wp_theme_preview");void 0===n?t.url=(0,a.F)(t.url,{wp_theme_preview:e}):""===n&&(t.url=i(t.url,"wp_theme_preview"))}if("string"==typeof t.path){const n=(0,r.d)(t.path,"wp_theme_preview");void 0===n?t.path=(0,a.F)(t.path,{wp_theme_preview:e}):""===n&&(t.path=i(t.path,"wp_theme_preview"))}return n(t)}},529:(e,t,n)=>{"use strict";n.d(t,{A:()=>o});var r=n(286);function a(e,t){return void 0!==(0,r.d)(e,t)}var s=n(568);const o=(e,t)=>("string"!=typeof e.url||a(e.url,"_locale")||(e.url=(0,s.F)(e.url,{_locale:"user"})),"string"!=typeof e.path||a(e.path,"_locale")||(e.path=(0,s.F)(e.path,{_locale:"user"})),t(e))},641:(e,t,n)=>{"use strict";n.d(t,{J:()=>o,f:()=>s});var r=n(43);const a=e=>{const t={code:"invalid_json",message:(0,r.__)("The response is not a valid JSON response.")};if(!e||!e.json)throw t;return e.json().catch((()=>{throw t}))},s=(e,t=!0)=>Promise.resolve(((e,t=!0)=>t?204===e.status?null:e.json?e.json():Promise.reject(e):e)(e,t)).catch((e=>o(e,t)));function o(e,t=!0){if(!t)throw e;return a(e).then((e=>{const t={code:"unknown_error",message:(0,r.__)("An unknown error occurred.")};throw e||t}))}},551:(e,t,n)=>{"use strict";n.d(t,{A:()=>f});const r=function(e){return"string"!=typeof e||""===e?(console.error("The namespace must be a non-empty string."),!1):!!/^[a-zA-Z][a-zA-Z0-9_.\-\/]*$/.test(e)||(console.error("The namespace can only contain numbers, letters, dashes, periods, underscores and slashes."),!1)};const a=function(e){return"string"!=typeof e||""===e?(console.error("The hook name must be a non-empty string."),!1):/^__/.test(e)?(console.error("The hook name cannot begin with `__`."),!1):!!/^[a-zA-Z][a-zA-Z0-9_.-]*$/.test(e)||(console.error("The hook name can only contain numbers, letters, dashes, periods and underscores."),!1)};const s=function(e,t){return function(n,s,o,i=10){const c=e[t];if(!a(n))return;if(!r(s))return;if("function"!=typeof o)return void console.error("The hook callback must be a function.");if("number"!=typeof i)return void console.error("If specified, the hook priority must be a number.");const l={callback:o,priority:i,namespace:s};if(c[n]){const e=c[n].handlers;let t;for(t=e.length;t>0&&!(i>=e[t-1].priority);t--);t===e.length?e[t]=l:e.splice(t,0,l),c.__current.forEach((e=>{e.name===n&&e.currentIndex>=t&&e.currentIndex++}))}else c[n]={handlers:[l],runs:0};"hookAdded"!==n&&e.doAction("hookAdded",n,s,o,i)}};const o=function(e,t,n=!1){return function(s,o){const i=e[t];if(!a(s))return;if(!n&&!r(o))return;if(!i[s])return 0;let c=0;if(n)c=i[s].handlers.length,i[s]={runs:i[s].runs,handlers:[]};else{const e=i[s].handlers;for(let t=e.length-1;t>=0;t--)e[t].namespace===o&&(e.splice(t,1),c++,i.__current.forEach((e=>{e.name===s&&e.currentIndex>=t&&e.currentIndex--})))}return"hookRemoved"!==s&&e.doAction("hookRemoved",s,o),c}};const i=function(e,t){return function(n,r){const a=e[t];return void 0!==r?n in a&&a[n].handlers.some((e=>e.namespace===r)):n in a}};const c=function(e,t,n=!1){return function(r,...a){const s=e[t];s[r]||(s[r]={handlers:[],runs:0}),s[r].runs++;const o=s[r].handlers;if(!o||!o.length)return n?a[0]:void 0;const i={name:r,currentIndex:0};for(s.__current.push(i);i.currentIndex<o.length;){const e=o[i.currentIndex].callback.apply(null,a);n&&(a[0]=e),i.currentIndex++}return s.__current.pop(),n?a[0]:void 0}};const l=function(e,t){return function(){var n;const r=e[t];return null!==(n=r.__current[r.__current.length-1]?.name)&&void 0!==n?n:null}};const u=function(e,t){return function(n){const r=e[t];return void 0===n?void 0!==r.__current[0]:!!r.__current[0]&&n===r.__current[0].name}};const p=function(e,t){return function(n){const r=e[t];if(a(n))return r[n]&&r[n].runs?r[n].runs:0}};class d{constructor(){this.actions=Object.create(null),this.actions.__current=[],this.filters=Object.create(null),this.filters.__current=[],this.addAction=s(this,"actions"),this.addFilter=s(this,"filters"),this.removeAction=o(this,"actions"),this.removeFilter=o(this,"filters"),this.hasAction=i(this,"actions"),this.hasFilter=i(this,"filters"),this.removeAllActions=o(this,"actions",!0),this.removeAllFilters=o(this,"filters",!0),this.doAction=c(this,"actions"),this.applyFilters=c(this,"filters",!0),this.currentAction=l(this,"actions"),this.currentFilter=l(this,"filters"),this.doingAction=u(this,"actions"),this.doingFilter=u(this,"filters"),this.didAction=p(this,"actions"),this.didFilter=p(this,"filters")}}const f=function(){return new d}},200:(e,t,n)=>{"use strict";n.d(t,{h:()=>o});var r=n(685);const a={plural_forms:e=>1===e?0:1},s=/^i18n\.(n?gettext|has_translation)(_|$)/,o=(e,t,n)=>{const o=new r.A({}),i=new Set,c=()=>{i.forEach((e=>e()))},l=(e,t="default")=>{o.data[t]={...o.data[t],...e},o.data[t][""]={...a,...o.data[t]?.[""]},delete o.pluralForms[t]},u=(e,t)=>{l(e,t),c()},p=(e="default",t,n,r,a)=>(o.data[e]||l(void 0,e),o.dcnpgettext(e,t,n,r,a)),d=(e="default")=>e,f=(e,t,r)=>{let a=p(r,t,e);return n?(a=n.applyFilters("i18n.gettext_with_context",a,e,t,r),n.applyFilters("i18n.gettext_with_context_"+d(r),a,e,t,r)):a};if(e&&u(e,t),n){const e=e=>{s.test(e)&&c()};n.addAction("hookAdded","core/i18n",e),n.addAction("hookRemoved","core/i18n",e)}return{getLocaleData:(e="default")=>o.data[e],setLocaleData:u,addLocaleData:(e,t="default")=>{o.data[t]={...o.data[t],...e,"":{...a,...o.data[t]?.[""],...e?.[""]}},delete o.pluralForms[t],c()},resetLocaleData:(e,t)=>{o.data={},o.pluralForms={},u(e,t)},subscribe:e=>(i.add(e),()=>i.delete(e)),__:(e,t)=>{let r=p(t,void 0,e);return n?(r=n.applyFilters("i18n.gettext",r,e,t),n.applyFilters("i18n.gettext_"+d(t),r,e,t)):r},_x:f,_n:(e,t,r,a)=>{let s=p(a,void 0,e,t,r);return n?(s=n.applyFilters("i18n.ngettext",s,e,t,r,a),n.applyFilters("i18n.ngettext_"+d(a),s,e,t,r,a)):s},_nx:(e,t,r,a,s)=>{let o=p(s,a,e,t,r);return n?(o=n.applyFilters("i18n.ngettext_with_context",o,e,t,r,a,s),n.applyFilters("i18n.ngettext_with_context_"+d(s),o,e,t,r,a,s)):o},isRTL:()=>"rtl"===f("ltr","text direction"),hasTranslation:(e,t,r)=>{const a=t?t+""+e:e;let s=!!o.data?.[null!=r?r:"default"]?.[a];return n&&(s=n.applyFilters("i18n.has_translation",s,e,t,r),s=n.applyFilters("i18n.has_translation_"+d(r),s,e,t,r)),s}}}},163:(e,t,n)=>{"use strict";n.d(t,{__:()=>o});var r=n(200),a=n(841);const s=(0,r.h)(void 0,void 0,a.se);s.getLocaleData.bind(s),s.setLocaleData.bind(s),s.resetLocaleData.bind(s),s.subscribe.bind(s);const o=s.__.bind(s);s._x.bind(s),s._n.bind(s),s._nx.bind(s),s.isRTL.bind(s),s.hasTranslation.bind(s)},569:(e,t,n)=>{"use strict";var r=n(428);n(304);(0,r.A)(console.error)},568:(e,t,n)=>{"use strict";n.d(t,{F:()=>s});var r=n(438),a=n(873);function s(e="",t){if(!t||!Object.keys(t).length)return e;let n=e;const s=e.indexOf("?");return-1!==s&&(t=Object.assign((0,r.u)(e),t),n=n.substr(0,s)),n+"?"+(0,a.G)(t)}},873:(e,t,n)=>{"use strict";function r(e){let t="";const n=Object.entries(e);let r;for(;r=n.shift();){let[e,a]=r;if(Array.isArray(a)||a&&a.constructor===Object){const t=Object.entries(a).reverse();for(const[r,a]of t)n.unshift([`${e}[${r}]`,a])}else void 0!==a&&(null===a&&(a=""),t+="&"+[e,a].map(encodeURIComponent).join("="))}return t.substr(1)}n.d(t,{G:()=>r})},286:(e,t,n)=>{"use strict";n.d(t,{d:()=>a});var r=n(438);function a(e,t){return(0,r.u)(e)[t]}},438:(e,t,n)=>{"use strict";function r(e){try{return decodeURIComponent(e)}catch(t){return e}}function a(e){return(function(e){let t;try{t=new URL(e,"http://example.com").search.substring(1)}catch(n){}if(t)return t}(e)||"").replace(/\+/g,"%20").split("&").reduce(((e,t)=>{const[n,a=""]=t.split("=").filter(Boolean).map(r);if(n){!function(e,t,n){const r=t.length,a=r-1;for(let s=0;s<r;s++){let r=t[s];!r&&Array.isArray(e)&&(r=e.length.toString()),r=["__proto__","constructor","prototype"].includes(r)?r.toUpperCase():r;const o=!isNaN(Number(t[s+1]));e[r]=s===a?n:e[r]||(o?[]:{}),Array.isArray(e[r])&&!o&&(e[r]={...e[r]}),e=e[r]}}(e,n.replace(/\]/g,"").split("["),a)}return e}),Object.create(null))}n.d(t,{u:()=>a})},541:(e,t,n)=>{"use strict";function r(e){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}function a(e){var t=function(e,t){if("object"!=r(e)||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var a=n.call(e,t||"default");if("object"!=r(a))return a;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==r(t)?t:t+""}function s(e,t,n){return(t=a(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function o(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=Array(t);n<t;n++)r[n]=e[n];return r}function i(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var n=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=n){var r,a,s,o,i=[],c=!0,l=!1;try{if(s=(n=n.call(e)).next,0===t){if(Object(n)!==n)return;c=!1}else for(;!(c=(r=s.call(n)).done)&&(i.push(r.value),i.length!==t);c=!0);}catch(e){l=!0,a=e}finally{try{if(!c&&null!=n.return&&(o=n.return(),Object(o)!==o))return}finally{if(l)throw a}}return i}}(e,t)||function(e,t){if(e){if("string"==typeof e)return o(e,t);var n={}.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?o(e,t):void 0}}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}n.r(t),n.d(t,{default:()=>g});var c=n(696);const l=function(e){var t=e.items,n=e.onPageDelete,r=i((0,c.useState)([]),2),a=r[0],s=r[1],o=i((0,c.useState)(null),2),l=o[0];o[1];(0,c.useEffect)((function(){"posts"in t&&s(t.posts)}),[t]);return l?c.createElement("tbody",null,c.createElement("tr",null,c.createElement("td",{colSpan:"6"},"Error: ",l.message))):0===a.length?c.createElement("tbody",null,c.createElement("tr",null,c.createElement("td",{colSpan:"6"},"No items found"))):c.createElement("tbody",null,a.map((function(e,t){return null===e.post?null:c.createElement("tr",{key:t},c.createElement("td",null,e.id),c.createElement("td",null,c.createElement("a",{href:e.enterprise_url,target:"_blank"},e.slug)),c.createElement("td",null,e.time),c.createElement("td",null,0===e.cached?"No":"Yes"),c.createElement("td",{className:"td-right"},c.createElement("button",{disabled:0===e.cached,className:"button button-primary","data-item-slug":e.slug,onClick:function(e){return function(e,t){e.preventDefault(),n(e,t)}(e,e.currentTarget)}},"Regenerate Cache")))})))};const u=function(e){var t=e.onSearch,n=i((0,c.useState)(""),2),r=n[0],a=n[1];return c.createElement("div",{id:"items-search"},c.createElement("form",null,c.createElement("div",{className:"span-input"},c.createElement("input",{id:"input-search-items",type:"text",name:"search-items",value:r,className:"input-text",placeholder:"Search …",onChange:function(e){a(e.target.value),""===e.target.value&&t("")}})),c.createElement("div",{className:"span-button"},c.createElement("button",{type:"submit",onClick:function(e){e.preventDefault(),t(r)},className:"button button-primary"},"Search"),c.createElement("button",{type:"reset",onClick:function(){event.preventDefault(),a(""),t("")},disabled:r?"":" disabled",className:"button button-secondary"},"Clear"))))};const p=function(e){var t=e.args,n=e.posts,r=e.onPageSelect,a=i((0,c.useState)({found:0,pages:1,posts:[]}),2),s=a[0],o=a[1],l=i((0,c.useState)(1),2),u=l[0],p=l[1];if((0,c.useEffect)((function(){o(n)}),[n,t]),0!==s.length&&0!==s.found){var d=function(e,t){return"last"===t&&e===s.pages||"first"===t&&1===e?" disabled":""},f=function(e,t){e.preventDefault();var n=parseInt(t.getAttribute("data-page"));p(n),r(n)};return c.createElement("div",{className:"tablenav bottom"},c.createElement("div",{className:"tablenav-pages"},c.createElement("span",{className:"displaying-num"},s.found," items"),c.createElement("span",{className:"pagination-links"},c.createElement("a",{className:"first-page button".concat(d(u,"first")),href:"#","data-page":1,onClick:function(e){return f(e,e.currentTarget)}},c.createElement("span",{className:"screen-reader-text"},"First page"),c.createElement("span",{"aria-hidden":"true"},"«")),c.createElement("a",{className:"prev-page button".concat(d(u,"first")),href:"#","data-page":Math.max(1,u-1),onClick:function(e){return f(e,e.currentTarget)}},c.createElement("span",{className:"screen-reader-text"},"Previous page"),c.createElement("span",{"aria-hidden":"true"},"‹")),c.createElement("span",{className:"screen-reader-text"},"Current Page"),c.createElement("span",{id:"table-paging",className:"paging-input"},c.createElement("span",{className:"tablenav-paging-text"},u," of",c.createElement("span",{className:"total-pages"}," ",s.pages))),c.createElement("a",{className:"next-page button".concat(d(u,"last")),href:"#","data-page":Math.min(s.pages,u+1),onClick:function(e){return f(e,e.currentTarget)}},c.createElement("span",{className:"screen-reader-text"},"Next page"),c.createElement("span",{"aria-hidden":"true"},"›")),c.createElement("a",{className:"last-page button".concat(d(u,"last")),href:"#","data-page":s.pages,onClick:function(e){return f(e,e.currentTarget)}},c.createElement("span",{className:"screen-reader-text"},"Last page"),c.createElement("span",{"aria-hidden":"true"},"»")))))}};var d=n(294),f=n(568);function h(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function m(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?h(Object(n),!0).forEach((function(t){s(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):h(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}const g=function(){var e=i((0,c.useState)([]),2),t=e[0],n=e[1],r=i((0,c.useState)(null),2),a=(r[0],r[1],i((0,c.useState)({page:1,per_page:15,search:""}),2)),s=a[0],o=a[1],h=(0,c.useCallback)((function(){(function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"GET";d.A.use(d.A.createRootURLMiddleware(window.codesoup_ilc.root));var n=Object.keys(e).length?(0,f.F)("/instapage-cache/v1/pages",e):"/instapage-cache/v1/pages";return(0,d.A)({path:n,method:t,credentials:"same-origin",headers:{"Content-Type":"application/json",Accept:"application/json","X-WP-Nonce":window.codesoup_ilc.nonce}})})(s).then((function(e){n(e)}))}),[s]);(0,c.useEffect)((function(){h()}),[h]);var g=function(e,t){e.preventDefault(),b(t.getAttribute("data-item-slug")).then((function(){h()}))},b=function(e){return d.A.use(d.A.createRootURLMiddleware(window.codesoup_ilc.root)),(0,d.A)({path:(0,f.F)("/instapage-cache/v1/delete",{slug:e}),method:"POST",credentials:"same-origin",headers:{"Content-Type":"application/json",Accept:"application/json","X-WP-Nonce":window.codesoup_ilc.nonce}})};return c.createElement("div",{className:"wrap"},c.createElement(u,{onSearch:function(e){o(m(m({},s),{},{search:e,page:1}))}}),c.createElement("table",{className:"wp-list-table widefat striped table-view-list posts"},c.createElement("thead",null,c.createElement("tr",null,c.createElement("th",null,"ID"),c.createElement("th",null,"Page URL"),c.createElement("th",null,"Published On"),c.createElement("th",null,"Cached"),c.createElement("th",{className:"td-right"},c.createElement("button",{className:"button button-primary","data-item-slug":"all",onClick:function(e){return g(e,e.currentTarget)}},"Regenerate All")))),c.createElement(l,{items:t,onPageDelete:g})),c.createElement(p,{args:s,posts:t,onPageSelect:function(e){o(m(m({},s),{},{page:e}))}}))}},304:(e,t,n)=>{var r;!function(){"use strict";var a={not_string:/[^s]/,not_bool:/[^t]/,not_type:/[^T]/,not_primitive:/[^v]/,number:/[diefg]/,numeric_arg:/[bcdiefguxX]/,json:/[j]/,not_json:/[^j]/,text:/^[^\x25]+/,modulo:/^\x25{2}/,placeholder:/^\x25(?:([1-9]\d*)\$|\(([^)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-gijostTuvxX])/,key:/^([a-z_][a-z_\d]*)/i,key_access:/^\.([a-z_][a-z_\d]*)/i,index_access:/^\[(\d+)\]/,sign:/^[+-]/};function s(e){return function(e,t){var n,r,o,i,c,l,u,p,d,f=1,h=e.length,m="";for(r=0;r<h;r++)if("string"==typeof e[r])m+=e[r];else if("object"==typeof e[r]){if((i=e[r]).keys)for(n=t[f],o=0;o<i.keys.length;o++){if(null==n)throw new Error(s('[sprintf] Cannot access property "%s" of undefined value "%s"',i.keys[o],i.keys[o-1]));n=n[i.keys[o]]}else n=i.param_no?t[i.param_no]:t[f++];if(a.not_type.test(i.type)&&a.not_primitive.test(i.type)&&n instanceof Function&&(n=n()),a.numeric_arg.test(i.type)&&"number"!=typeof n&&isNaN(n))throw new TypeError(s("[sprintf] expecting number but found %T",n));switch(a.number.test(i.type)&&(p=n>=0),i.type){case"b":n=parseInt(n,10).toString(2);break;case"c":n=String.fromCharCode(parseInt(n,10));break;case"d":case"i":n=parseInt(n,10);break;case"j":n=JSON.stringify(n,null,i.width?parseInt(i.width):0);break;case"e":n=i.precision?parseFloat(n).toExponential(i.precision):parseFloat(n).toExponential();break;case"f":n=i.precision?parseFloat(n).toFixed(i.precision):parseFloat(n);break;case"g":n=i.precision?String(Number(n.toPrecision(i.precision))):parseFloat(n);break;case"o":n=(parseInt(n,10)>>>0).toString(8);break;case"s":n=String(n),n=i.precision?n.substring(0,i.precision):n;break;case"t":n=String(!!n),n=i.precision?n.substring(0,i.precision):n;break;case"T":n=Object.prototype.toString.call(n).slice(8,-1).toLowerCase(),n=i.precision?n.substring(0,i.precision):n;break;case"u":n=parseInt(n,10)>>>0;break;case"v":n=n.valueOf(),n=i.precision?n.substring(0,i.precision):n;break;case"x":n=(parseInt(n,10)>>>0).toString(16);break;case"X":n=(parseInt(n,10)>>>0).toString(16).toUpperCase()}a.json.test(i.type)?m+=n:(!a.number.test(i.type)||p&&!i.sign?d="":(d=p?"+":"-",n=n.toString().replace(a.sign,"")),l=i.pad_char?"0"===i.pad_char?"0":i.pad_char.charAt(1):" ",u=i.width-(d+n).length,c=i.width&&u>0?l.repeat(u):"",m+=i.align?d+n+c:"0"===l?d+c+n:c+d+n)}return m}(function(e){if(i[e])return i[e];var t,n=e,r=[],s=0;for(;n;){if(null!==(t=a.text.exec(n)))r.push(t[0]);else if(null!==(t=a.modulo.exec(n)))r.push("%");else{if(null===(t=a.placeholder.exec(n)))throw new SyntaxError("[sprintf] unexpected placeholder");if(t[2]){s|=1;var o=[],c=t[2],l=[];if(null===(l=a.key.exec(c)))throw new SyntaxError("[sprintf] failed to parse named argument key");for(o.push(l[1]);""!==(c=c.substring(l[0].length));)if(null!==(l=a.key_access.exec(c)))o.push(l[1]);else{if(null===(l=a.index_access.exec(c)))throw new SyntaxError("[sprintf] failed to parse named argument key");o.push(l[1])}t[2]=o}else s|=2;if(3===s)throw new Error("[sprintf] mixing positional and named placeholders is not (yet) supported");r.push({placeholder:t[0],param_no:t[1],keys:t[2],sign:t[3],pad_char:t[4],align:t[5],width:t[6],precision:t[7],type:t[8]})}n=n.substring(t[0].length)}return i[e]=r}(e),arguments)}function o(e,t){return s.apply(null,[e].concat(t||[]))}var i=Object.create(null);s,o,"undefined"!=typeof window&&(window.sprintf=s,window.vsprintf=o,void 0===(r=function(){return{sprintf:s,vsprintf:o}}.call(t,n,t,e))||(e.exports=r))}()}}]);