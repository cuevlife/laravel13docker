function Ge(e,t){return function(){return e.apply(t,arguments)}}const{toString:Rt}=Object.prototype,{getPrototypeOf:Ee}=Object,{iterator:oe,toStringTag:Qe}=Symbol,ie=(e=>t=>{const n=Rt.call(t);return e[n]||(e[n]=n.slice(8,-1).toLowerCase())})(Object.create(null)),P=e=>(e=e.toLowerCase(),t=>ie(t)===e),ae=e=>t=>typeof t===e,{isArray:q}=Array,I=ae("undefined");function $(e){return e!==null&&!I(e)&&e.constructor!==null&&!I(e.constructor)&&O(e.constructor.isBuffer)&&e.constructor.isBuffer(e)}const Ye=P("ArrayBuffer");function St(e){let t;return typeof ArrayBuffer<"u"&&ArrayBuffer.isView?t=ArrayBuffer.isView(e):t=e&&e.buffer&&Ye(e.buffer),t}const At=ae("string"),O=ae("function"),et=ae("number"),J=e=>e!==null&&typeof e=="object",Mt=e=>e===!0||e===!1,te=e=>{if(ie(e)!=="object")return!1;const t=Ee(e);return(t===null||t===Object.prototype||Object.getPrototypeOf(t)===null)&&!(Qe in e)&&!(oe in e)},Ot=e=>{if(!J(e)||$(e))return!1;try{return Object.keys(e).length===0&&Object.getPrototypeOf(e)===Object.prototype}catch{return!1}},vt=P("Date"),Ct=P("File"),Tt=e=>!!(e&&typeof e.uri<"u"),Nt=e=>e&&typeof e.getParts<"u",Lt=P("Blob"),Pt=P("FileList"),_t=e=>J(e)&&O(e.pipe);function Ft(){return typeof globalThis<"u"?globalThis:typeof self<"u"?self:typeof window<"u"?window:typeof global<"u"?global:{}}const Pe=Ft(),_e=typeof Pe.FormData<"u"?Pe.FormData:void 0,Ut=e=>{let t;return e&&(_e&&e instanceof _e||O(e.append)&&((t=ie(e))==="formdata"||t==="object"&&O(e.toString)&&e.toString()==="[object FormData]"))},Bt=P("URLSearchParams"),[Dt,kt,Ht,jt]=["ReadableStream","Request","Response","Headers"].map(P),It=e=>e.trim?e.trim():e.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"");function W(e,t,{allOwnKeys:n=!1}={}){if(e===null||typeof e>"u")return;let r,s;if(typeof e!="object"&&(e=[e]),q(e))for(r=0,s=e.length;r<s;r++)t.call(null,e[r],r,e);else{if($(e))return;const o=n?Object.getOwnPropertyNames(e):Object.keys(e),i=o.length;let c;for(r=0;r<i;r++)c=o[r],t.call(null,e[c],c,e)}}function tt(e,t){if($(e))return null;t=t.toLowerCase();const n=Object.keys(e);let r=n.length,s;for(;r-- >0;)if(s=n[r],t===s.toLowerCase())return s;return null}const D=typeof globalThis<"u"?globalThis:typeof self<"u"?self:typeof window<"u"?window:global,nt=e=>!I(e)&&e!==D;function ye(){const{caseless:e,skipUndefined:t}=nt(this)&&this||{},n={},r=(s,o)=>{if(o==="__proto__"||o==="constructor"||o==="prototype")return;const i=e&&tt(n,o)||o;te(n[i])&&te(s)?n[i]=ye(n[i],s):te(s)?n[i]=ye({},s):q(s)?n[i]=s.slice():(!t||!I(s))&&(n[i]=s)};for(let s=0,o=arguments.length;s<o;s++)arguments[s]&&W(arguments[s],r);return n}const qt=(e,t,n,{allOwnKeys:r}={})=>(W(t,(s,o)=>{n&&O(s)?Object.defineProperty(e,o,{value:Ge(s,n),writable:!0,enumerable:!0,configurable:!0}):Object.defineProperty(e,o,{value:s,writable:!0,enumerable:!0,configurable:!0})},{allOwnKeys:r}),e),Vt=e=>(e.charCodeAt(0)===65279&&(e=e.slice(1)),e),zt=(e,t,n,r)=>{e.prototype=Object.create(t.prototype,r),Object.defineProperty(e.prototype,"constructor",{value:e,writable:!0,enumerable:!1,configurable:!0}),Object.defineProperty(e,"super",{value:t.prototype}),n&&Object.assign(e.prototype,n)},$t=(e,t,n,r)=>{let s,o,i;const c={};if(t=t||{},e==null)return t;do{for(s=Object.getOwnPropertyNames(e),o=s.length;o-- >0;)i=s[o],(!r||r(i,e,t))&&!c[i]&&(t[i]=e[i],c[i]=!0);e=n!==!1&&Ee(e)}while(e&&(!n||n(e,t))&&e!==Object.prototype);return t},Jt=(e,t,n)=>{e=String(e),(n===void 0||n>e.length)&&(n=e.length),n-=t.length;const r=e.indexOf(t,n);return r!==-1&&r===n},Wt=e=>{if(!e)return null;if(q(e))return e;let t=e.length;if(!et(t))return null;const n=new Array(t);for(;t-- >0;)n[t]=e[t];return n},Kt=(e=>t=>e&&t instanceof e)(typeof Uint8Array<"u"&&Ee(Uint8Array)),Xt=(e,t)=>{const r=(e&&e[oe]).call(e);let s;for(;(s=r.next())&&!s.done;){const o=s.value;t.call(e,o[0],o[1])}},Zt=(e,t)=>{let n;const r=[];for(;(n=e.exec(t))!==null;)r.push(n);return r},Gt=P("HTMLFormElement"),Qt=e=>e.toLowerCase().replace(/[-_\s]([a-z\d])(\w*)/g,function(n,r,s){return r.toUpperCase()+s}),Fe=(({hasOwnProperty:e})=>(t,n)=>e.call(t,n))(Object.prototype),Yt=P("RegExp"),rt=(e,t)=>{const n=Object.getOwnPropertyDescriptors(e),r={};W(n,(s,o)=>{let i;(i=t(s,o,e))!==!1&&(r[o]=i||s)}),Object.defineProperties(e,r)},en=e=>{rt(e,(t,n)=>{if(O(e)&&["arguments","caller","callee"].indexOf(n)!==-1)return!1;const r=e[n];if(O(r)){if(t.enumerable=!1,"writable"in t){t.writable=!1;return}t.set||(t.set=()=>{throw Error("Can not rewrite read-only method '"+n+"'")})}})},tn=(e,t)=>{const n={},r=s=>{s.forEach(o=>{n[o]=!0})};return q(e)?r(e):r(String(e).split(t)),n},nn=()=>{},rn=(e,t)=>e!=null&&Number.isFinite(e=+e)?e:t;function sn(e){return!!(e&&O(e.append)&&e[Qe]==="FormData"&&e[oe])}const on=e=>{const t=new Array(10),n=(r,s)=>{if(J(r)){if(t.indexOf(r)>=0)return;if($(r))return r;if(!("toJSON"in r)){t[s]=r;const o=q(r)?[]:{};return W(r,(i,c)=>{const p=n(i,s+1);!I(p)&&(o[c]=p)}),t[s]=void 0,o}}return r};return n(e,0)},an=P("AsyncFunction"),cn=e=>e&&(J(e)||O(e))&&O(e.then)&&O(e.catch),st=((e,t)=>e?setImmediate:t?((n,r)=>(D.addEventListener("message",({source:s,data:o})=>{s===D&&o===n&&r.length&&r.shift()()},!1),s=>{r.push(s),D.postMessage(n,"*")}))(`axios@${Math.random()}`,[]):n=>setTimeout(n))(typeof setImmediate=="function",O(D.postMessage)),ln=typeof queueMicrotask<"u"?queueMicrotask.bind(D):typeof process<"u"&&process.nextTick||st,un=e=>e!=null&&O(e[oe]),a={isArray:q,isArrayBuffer:Ye,isBuffer:$,isFormData:Ut,isArrayBufferView:St,isString:At,isNumber:et,isBoolean:Mt,isObject:J,isPlainObject:te,isEmptyObject:Ot,isReadableStream:Dt,isRequest:kt,isResponse:Ht,isHeaders:jt,isUndefined:I,isDate:vt,isFile:Ct,isReactNativeBlob:Tt,isReactNative:Nt,isBlob:Lt,isRegExp:Yt,isFunction:O,isStream:_t,isURLSearchParams:Bt,isTypedArray:Kt,isFileList:Pt,forEach:W,merge:ye,extend:qt,trim:It,stripBOM:Vt,inherits:zt,toFlatObject:$t,kindOf:ie,kindOfTest:P,endsWith:Jt,toArray:Wt,forEachEntry:Xt,matchAll:Zt,isHTMLForm:Gt,hasOwnProperty:Fe,hasOwnProp:Fe,reduceDescriptors:rt,freezeMethods:en,toObjectSet:tn,toCamelCase:Qt,noop:nn,toFiniteNumber:rn,findKey:tt,global:D,isContextDefined:nt,isSpecCompliantForm:sn,toJSONObject:on,isAsyncFn:an,isThenable:cn,setImmediate:st,asap:ln,isIterable:un};let g=class ot extends Error{static from(t,n,r,s,o,i){const c=new ot(t.message,n||t.code,r,s,o);return c.cause=t,c.name=t.name,t.status!=null&&c.status==null&&(c.status=t.status),i&&Object.assign(c,i),c}constructor(t,n,r,s,o){super(t),Object.defineProperty(this,"message",{value:t,enumerable:!0,writable:!0,configurable:!0}),this.name="AxiosError",this.isAxiosError=!0,n&&(this.code=n),r&&(this.config=r),s&&(this.request=s),o&&(this.response=o,this.status=o.status)}toJSON(){return{message:this.message,name:this.name,description:this.description,number:this.number,fileName:this.fileName,lineNumber:this.lineNumber,columnNumber:this.columnNumber,stack:this.stack,config:a.toJSONObject(this.config),code:this.code,status:this.status}}};g.ERR_BAD_OPTION_VALUE="ERR_BAD_OPTION_VALUE";g.ERR_BAD_OPTION="ERR_BAD_OPTION";g.ECONNABORTED="ECONNABORTED";g.ETIMEDOUT="ETIMEDOUT";g.ERR_NETWORK="ERR_NETWORK";g.ERR_FR_TOO_MANY_REDIRECTS="ERR_FR_TOO_MANY_REDIRECTS";g.ERR_DEPRECATED="ERR_DEPRECATED";g.ERR_BAD_RESPONSE="ERR_BAD_RESPONSE";g.ERR_BAD_REQUEST="ERR_BAD_REQUEST";g.ERR_CANCELED="ERR_CANCELED";g.ERR_NOT_SUPPORT="ERR_NOT_SUPPORT";g.ERR_INVALID_URL="ERR_INVALID_URL";const dn=null;function ge(e){return a.isPlainObject(e)||a.isArray(e)}function it(e){return a.endsWith(e,"[]")?e.slice(0,-2):e}function he(e,t,n){return e?e.concat(t).map(function(s,o){return s=it(s),!n&&o?"["+s+"]":s}).join(n?".":""):t}function hn(e){return a.isArray(e)&&!e.some(ge)}const fn=a.toFlatObject(a,{},null,function(t){return/^is[A-Z]/.test(t)});function ce(e,t,n){if(!a.isObject(e))throw new TypeError("target must be an object");t=t||new FormData,n=a.toFlatObject(n,{metaTokens:!0,dots:!1,indexes:!1},!1,function(y,f){return!a.isUndefined(f[y])});const r=n.metaTokens,s=n.visitor||l,o=n.dots,i=n.indexes,p=(n.Blob||typeof Blob<"u"&&Blob)&&a.isSpecCompliantForm(t);if(!a.isFunction(s))throw new TypeError("visitor must be a function");function d(u){if(u===null)return"";if(a.isDate(u))return u.toISOString();if(a.isBoolean(u))return u.toString();if(!p&&a.isBlob(u))throw new g("Blob is not supported. Use a Buffer instead.");return a.isArrayBuffer(u)||a.isTypedArray(u)?p&&typeof Blob=="function"?new Blob([u]):Buffer.from(u):u}function l(u,y,f){let x=u;if(a.isReactNative(t)&&a.isReactNativeBlob(u))return t.append(he(f,y,o),d(u)),!1;if(u&&!f&&typeof u=="object"){if(a.endsWith(y,"{}"))y=r?y:y.slice(0,-2),u=JSON.stringify(u);else if(a.isArray(u)&&hn(u)||(a.isFileList(u)||a.endsWith(y,"[]"))&&(x=a.toArray(u)))return y=it(y),x.forEach(function(R,A){!(a.isUndefined(R)||R===null)&&t.append(i===!0?he([y],A,o):i===null?y:y+"[]",d(R))}),!1}return ge(u)?!0:(t.append(he(f,y,o),d(u)),!1)}const m=[],w=Object.assign(fn,{defaultVisitor:l,convertValue:d,isVisitable:ge});function E(u,y){if(!a.isUndefined(u)){if(m.indexOf(u)!==-1)throw Error("Circular reference detected in "+y.join("."));m.push(u),a.forEach(u,function(x,C){(!(a.isUndefined(x)||x===null)&&s.call(t,x,a.isString(C)?C.trim():C,y,w))===!0&&E(x,y?y.concat(C):[C])}),m.pop()}}if(!a.isObject(e))throw new TypeError("data must be an object");return E(e),t}function Ue(e){const t={"!":"%21","'":"%27","(":"%28",")":"%29","~":"%7E","%20":"+","%00":"\0"};return encodeURIComponent(e).replace(/[!'()~]|%20|%00/g,function(r){return t[r]})}function Re(e,t){this._pairs=[],e&&ce(e,this,t)}const at=Re.prototype;at.append=function(t,n){this._pairs.push([t,n])};at.toString=function(t){const n=t?function(r){return t.call(this,r,Ue)}:Ue;return this._pairs.map(function(s){return n(s[0])+"="+n(s[1])},"").join("&")};function pn(e){return encodeURIComponent(e).replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"+")}function ct(e,t,n){if(!t)return e;const r=n&&n.encode||pn,s=a.isFunction(n)?{serialize:n}:n,o=s&&s.serialize;let i;if(o?i=o(t,s):i=a.isURLSearchParams(t)?t.toString():new Re(t,s).toString(r),i){const c=e.indexOf("#");c!==-1&&(e=e.slice(0,c)),e+=(e.indexOf("?")===-1?"?":"&")+i}return e}class Be{constructor(){this.handlers=[]}use(t,n,r){return this.handlers.push({fulfilled:t,rejected:n,synchronous:r?r.synchronous:!1,runWhen:r?r.runWhen:null}),this.handlers.length-1}eject(t){this.handlers[t]&&(this.handlers[t]=null)}clear(){this.handlers&&(this.handlers=[])}forEach(t){a.forEach(this.handlers,function(r){r!==null&&t(r)})}}const Se={silentJSONParsing:!0,forcedJSONParsing:!0,clarifyTimeoutError:!1,legacyInterceptorReqResOrdering:!0},mn=typeof URLSearchParams<"u"?URLSearchParams:Re,yn=typeof FormData<"u"?FormData:null,gn=typeof Blob<"u"?Blob:null,wn={isBrowser:!0,classes:{URLSearchParams:mn,FormData:yn,Blob:gn},protocols:["http","https","file","blob","url","data"]},Ae=typeof window<"u"&&typeof document<"u",we=typeof navigator=="object"&&navigator||void 0,bn=Ae&&(!we||["ReactNative","NativeScript","NS"].indexOf(we.product)<0),xn=typeof WorkerGlobalScope<"u"&&self instanceof WorkerGlobalScope&&typeof self.importScripts=="function",En=Ae&&window.location.href||"http://localhost",Rn=Object.freeze(Object.defineProperty({__proto__:null,hasBrowserEnv:Ae,hasStandardBrowserEnv:bn,hasStandardBrowserWebWorkerEnv:xn,navigator:we,origin:En},Symbol.toStringTag,{value:"Module"})),S={...Rn,...wn};function Sn(e,t){return ce(e,new S.classes.URLSearchParams,{visitor:function(n,r,s,o){return S.isNode&&a.isBuffer(n)?(this.append(r,n.toString("base64")),!1):o.defaultVisitor.apply(this,arguments)},...t})}function An(e){return a.matchAll(/\w+|\[(\w*)]/g,e).map(t=>t[0]==="[]"?"":t[1]||t[0])}function Mn(e){const t={},n=Object.keys(e);let r;const s=n.length;let o;for(r=0;r<s;r++)o=n[r],t[o]=e[o];return t}function lt(e){function t(n,r,s,o){let i=n[o++];if(i==="__proto__")return!0;const c=Number.isFinite(+i),p=o>=n.length;return i=!i&&a.isArray(s)?s.length:i,p?(a.hasOwnProp(s,i)?s[i]=[s[i],r]:s[i]=r,!c):((!s[i]||!a.isObject(s[i]))&&(s[i]=[]),t(n,r,s[i],o)&&a.isArray(s[i])&&(s[i]=Mn(s[i])),!c)}if(a.isFormData(e)&&a.isFunction(e.entries)){const n={};return a.forEachEntry(e,(r,s)=>{t(An(r),s,n,0)}),n}return null}function On(e,t,n){if(a.isString(e))try{return(t||JSON.parse)(e),a.trim(e)}catch(r){if(r.name!=="SyntaxError")throw r}return(n||JSON.stringify)(e)}const K={transitional:Se,adapter:["xhr","http","fetch"],transformRequest:[function(t,n){const r=n.getContentType()||"",s=r.indexOf("application/json")>-1,o=a.isObject(t);if(o&&a.isHTMLForm(t)&&(t=new FormData(t)),a.isFormData(t))return s?JSON.stringify(lt(t)):t;if(a.isArrayBuffer(t)||a.isBuffer(t)||a.isStream(t)||a.isFile(t)||a.isBlob(t)||a.isReadableStream(t))return t;if(a.isArrayBufferView(t))return t.buffer;if(a.isURLSearchParams(t))return n.setContentType("application/x-www-form-urlencoded;charset=utf-8",!1),t.toString();let c;if(o){if(r.indexOf("application/x-www-form-urlencoded")>-1)return Sn(t,this.formSerializer).toString();if((c=a.isFileList(t))||r.indexOf("multipart/form-data")>-1){const p=this.env&&this.env.FormData;return ce(c?{"files[]":t}:t,p&&new p,this.formSerializer)}}return o||s?(n.setContentType("application/json",!1),On(t)):t}],transformResponse:[function(t){const n=this.transitional||K.transitional,r=n&&n.forcedJSONParsing,s=this.responseType==="json";if(a.isResponse(t)||a.isReadableStream(t))return t;if(t&&a.isString(t)&&(r&&!this.responseType||s)){const i=!(n&&n.silentJSONParsing)&&s;try{return JSON.parse(t,this.parseReviver)}catch(c){if(i)throw c.name==="SyntaxError"?g.from(c,g.ERR_BAD_RESPONSE,this,null,this.response):c}}return t}],timeout:0,xsrfCookieName:"XSRF-TOKEN",xsrfHeaderName:"X-XSRF-TOKEN",maxContentLength:-1,maxBodyLength:-1,env:{FormData:S.classes.FormData,Blob:S.classes.Blob},validateStatus:function(t){return t>=200&&t<300},headers:{common:{Accept:"application/json, text/plain, */*","Content-Type":void 0}}};a.forEach(["delete","get","head","post","put","patch"],e=>{K.headers[e]={}});const vn=a.toObjectSet(["age","authorization","content-length","content-type","etag","expires","from","host","if-modified-since","if-unmodified-since","last-modified","location","max-forwards","proxy-authorization","referer","retry-after","user-agent"]),Cn=e=>{const t={};let n,r,s;return e&&e.split(`
`).forEach(function(i){s=i.indexOf(":"),n=i.substring(0,s).trim().toLowerCase(),r=i.substring(s+1).trim(),!(!n||t[n]&&vn[n])&&(n==="set-cookie"?t[n]?t[n].push(r):t[n]=[r]:t[n]=t[n]?t[n]+", "+r:r)}),t},De=Symbol("internals");function z(e){return e&&String(e).trim().toLowerCase()}function ne(e){return e===!1||e==null?e:a.isArray(e)?e.map(ne):String(e).replace(/[\r\n]+$/,"")}function Tn(e){const t=Object.create(null),n=/([^\s,;=]+)\s*(?:=\s*([^,;]+))?/g;let r;for(;r=n.exec(e);)t[r[1]]=r[2];return t}const Nn=e=>/^[-_a-zA-Z0-9^`|~,!#$%&'*+.]+$/.test(e.trim());function fe(e,t,n,r,s){if(a.isFunction(r))return r.call(this,t,n);if(s&&(t=n),!!a.isString(t)){if(a.isString(r))return t.indexOf(r)!==-1;if(a.isRegExp(r))return r.test(t)}}function Ln(e){return e.trim().toLowerCase().replace(/([a-z\d])(\w*)/g,(t,n,r)=>n.toUpperCase()+r)}function Pn(e,t){const n=a.toCamelCase(" "+t);["get","set","has"].forEach(r=>{Object.defineProperty(e,r+n,{value:function(s,o,i){return this[r].call(this,t,s,o,i)},configurable:!0})})}let v=class{constructor(t){t&&this.set(t)}set(t,n,r){const s=this;function o(c,p,d){const l=z(p);if(!l)throw new Error("header name must be a non-empty string");const m=a.findKey(s,l);(!m||s[m]===void 0||d===!0||d===void 0&&s[m]!==!1)&&(s[m||p]=ne(c))}const i=(c,p)=>a.forEach(c,(d,l)=>o(d,l,p));if(a.isPlainObject(t)||t instanceof this.constructor)i(t,n);else if(a.isString(t)&&(t=t.trim())&&!Nn(t))i(Cn(t),n);else if(a.isObject(t)&&a.isIterable(t)){let c={},p,d;for(const l of t){if(!a.isArray(l))throw TypeError("Object iterator must return a key-value pair");c[d=l[0]]=(p=c[d])?a.isArray(p)?[...p,l[1]]:[p,l[1]]:l[1]}i(c,n)}else t!=null&&o(n,t,r);return this}get(t,n){if(t=z(t),t){const r=a.findKey(this,t);if(r){const s=this[r];if(!n)return s;if(n===!0)return Tn(s);if(a.isFunction(n))return n.call(this,s,r);if(a.isRegExp(n))return n.exec(s);throw new TypeError("parser must be boolean|regexp|function")}}}has(t,n){if(t=z(t),t){const r=a.findKey(this,t);return!!(r&&this[r]!==void 0&&(!n||fe(this,this[r],r,n)))}return!1}delete(t,n){const r=this;let s=!1;function o(i){if(i=z(i),i){const c=a.findKey(r,i);c&&(!n||fe(r,r[c],c,n))&&(delete r[c],s=!0)}}return a.isArray(t)?t.forEach(o):o(t),s}clear(t){const n=Object.keys(this);let r=n.length,s=!1;for(;r--;){const o=n[r];(!t||fe(this,this[o],o,t,!0))&&(delete this[o],s=!0)}return s}normalize(t){const n=this,r={};return a.forEach(this,(s,o)=>{const i=a.findKey(r,o);if(i){n[i]=ne(s),delete n[o];return}const c=t?Ln(o):String(o).trim();c!==o&&delete n[o],n[c]=ne(s),r[c]=!0}),this}concat(...t){return this.constructor.concat(this,...t)}toJSON(t){const n=Object.create(null);return a.forEach(this,(r,s)=>{r!=null&&r!==!1&&(n[s]=t&&a.isArray(r)?r.join(", "):r)}),n}[Symbol.iterator](){return Object.entries(this.toJSON())[Symbol.iterator]()}toString(){return Object.entries(this.toJSON()).map(([t,n])=>t+": "+n).join(`
`)}getSetCookie(){return this.get("set-cookie")||[]}get[Symbol.toStringTag](){return"AxiosHeaders"}static from(t){return t instanceof this?t:new this(t)}static concat(t,...n){const r=new this(t);return n.forEach(s=>r.set(s)),r}static accessor(t){const r=(this[De]=this[De]={accessors:{}}).accessors,s=this.prototype;function o(i){const c=z(i);r[c]||(Pn(s,i),r[c]=!0)}return a.isArray(t)?t.forEach(o):o(t),this}};v.accessor(["Content-Type","Content-Length","Accept","Accept-Encoding","User-Agent","Authorization"]);a.reduceDescriptors(v.prototype,({value:e},t)=>{let n=t[0].toUpperCase()+t.slice(1);return{get:()=>e,set(r){this[n]=r}}});a.freezeMethods(v);function pe(e,t){const n=this||K,r=t||n,s=v.from(r.headers);let o=r.data;return a.forEach(e,function(c){o=c.call(n,o,s.normalize(),t?t.status:void 0)}),s.normalize(),o}function ut(e){return!!(e&&e.__CANCEL__)}let X=class extends g{constructor(t,n,r){super(t??"canceled",g.ERR_CANCELED,n,r),this.name="CanceledError",this.__CANCEL__=!0}};function dt(e,t,n){const r=n.config.validateStatus;!n.status||!r||r(n.status)?e(n):t(new g("Request failed with status code "+n.status,[g.ERR_BAD_REQUEST,g.ERR_BAD_RESPONSE][Math.floor(n.status/100)-4],n.config,n.request,n))}function _n(e){const t=/^([-+\w]{1,25})(:?\/\/|:)/.exec(e);return t&&t[1]||""}function Fn(e,t){e=e||10;const n=new Array(e),r=new Array(e);let s=0,o=0,i;return t=t!==void 0?t:1e3,function(p){const d=Date.now(),l=r[o];i||(i=d),n[s]=p,r[s]=d;let m=o,w=0;for(;m!==s;)w+=n[m++],m=m%e;if(s=(s+1)%e,s===o&&(o=(o+1)%e),d-i<t)return;const E=l&&d-l;return E?Math.round(w*1e3/E):void 0}}function Un(e,t){let n=0,r=1e3/t,s,o;const i=(d,l=Date.now())=>{n=l,s=null,o&&(clearTimeout(o),o=null),e(...d)};return[(...d)=>{const l=Date.now(),m=l-n;m>=r?i(d,l):(s=d,o||(o=setTimeout(()=>{o=null,i(s)},r-m)))},()=>s&&i(s)]}const se=(e,t,n=3)=>{let r=0;const s=Fn(50,250);return Un(o=>{const i=o.loaded,c=o.lengthComputable?o.total:void 0,p=i-r,d=s(p),l=i<=c;r=i;const m={loaded:i,total:c,progress:c?i/c:void 0,bytes:p,rate:d||void 0,estimated:d&&c&&l?(c-i)/d:void 0,event:o,lengthComputable:c!=null,[t?"download":"upload"]:!0};e(m)},n)},ke=(e,t)=>{const n=e!=null;return[r=>t[0]({lengthComputable:n,total:e,loaded:r}),t[1]]},He=e=>(...t)=>a.asap(()=>e(...t)),Bn=S.hasStandardBrowserEnv?((e,t)=>n=>(n=new URL(n,S.origin),e.protocol===n.protocol&&e.host===n.host&&(t||e.port===n.port)))(new URL(S.origin),S.navigator&&/(msie|trident)/i.test(S.navigator.userAgent)):()=>!0,Dn=S.hasStandardBrowserEnv?{write(e,t,n,r,s,o,i){if(typeof document>"u")return;const c=[`${e}=${encodeURIComponent(t)}`];a.isNumber(n)&&c.push(`expires=${new Date(n).toUTCString()}`),a.isString(r)&&c.push(`path=${r}`),a.isString(s)&&c.push(`domain=${s}`),o===!0&&c.push("secure"),a.isString(i)&&c.push(`SameSite=${i}`),document.cookie=c.join("; ")},read(e){if(typeof document>"u")return null;const t=document.cookie.match(new RegExp("(?:^|; )"+e+"=([^;]*)"));return t?decodeURIComponent(t[1]):null},remove(e){this.write(e,"",Date.now()-864e5,"/")}}:{write(){},read(){return null},remove(){}};function kn(e){return typeof e!="string"?!1:/^([a-z][a-z\d+\-.]*:)?\/\//i.test(e)}function Hn(e,t){return t?e.replace(/\/?\/$/,"")+"/"+t.replace(/^\/+/,""):e}function ht(e,t,n){let r=!kn(t);return e&&(r||n==!1)?Hn(e,t):t}const je=e=>e instanceof v?{...e}:e;function H(e,t){t=t||{};const n={};function r(d,l,m,w){return a.isPlainObject(d)&&a.isPlainObject(l)?a.merge.call({caseless:w},d,l):a.isPlainObject(l)?a.merge({},l):a.isArray(l)?l.slice():l}function s(d,l,m,w){if(a.isUndefined(l)){if(!a.isUndefined(d))return r(void 0,d,m,w)}else return r(d,l,m,w)}function o(d,l){if(!a.isUndefined(l))return r(void 0,l)}function i(d,l){if(a.isUndefined(l)){if(!a.isUndefined(d))return r(void 0,d)}else return r(void 0,l)}function c(d,l,m){if(m in t)return r(d,l);if(m in e)return r(void 0,d)}const p={url:o,method:o,data:o,baseURL:i,transformRequest:i,transformResponse:i,paramsSerializer:i,timeout:i,timeoutMessage:i,withCredentials:i,withXSRFToken:i,adapter:i,responseType:i,xsrfCookieName:i,xsrfHeaderName:i,onUploadProgress:i,onDownloadProgress:i,decompress:i,maxContentLength:i,maxBodyLength:i,beforeRedirect:i,transport:i,httpAgent:i,httpsAgent:i,cancelToken:i,socketPath:i,responseEncoding:i,validateStatus:c,headers:(d,l,m)=>s(je(d),je(l),m,!0)};return a.forEach(Object.keys({...e,...t}),function(l){if(l==="__proto__"||l==="constructor"||l==="prototype")return;const m=a.hasOwnProp(p,l)?p[l]:s,w=m(e[l],t[l],l);a.isUndefined(w)&&m!==c||(n[l]=w)}),n}const ft=e=>{const t=H({},e);let{data:n,withXSRFToken:r,xsrfHeaderName:s,xsrfCookieName:o,headers:i,auth:c}=t;if(t.headers=i=v.from(i),t.url=ct(ht(t.baseURL,t.url,t.allowAbsoluteUrls),e.params,e.paramsSerializer),c&&i.set("Authorization","Basic "+btoa((c.username||"")+":"+(c.password?unescape(encodeURIComponent(c.password)):""))),a.isFormData(n)){if(S.hasStandardBrowserEnv||S.hasStandardBrowserWebWorkerEnv)i.setContentType(void 0);else if(a.isFunction(n.getHeaders)){const p=n.getHeaders(),d=["content-type","content-length"];Object.entries(p).forEach(([l,m])=>{d.includes(l.toLowerCase())&&i.set(l,m)})}}if(S.hasStandardBrowserEnv&&(r&&a.isFunction(r)&&(r=r(t)),r||r!==!1&&Bn(t.url))){const p=s&&o&&Dn.read(o);p&&i.set(s,p)}return t},jn=typeof XMLHttpRequest<"u",In=jn&&function(e){return new Promise(function(n,r){const s=ft(e);let o=s.data;const i=v.from(s.headers).normalize();let{responseType:c,onUploadProgress:p,onDownloadProgress:d}=s,l,m,w,E,u;function y(){E&&E(),u&&u(),s.cancelToken&&s.cancelToken.unsubscribe(l),s.signal&&s.signal.removeEventListener("abort",l)}let f=new XMLHttpRequest;f.open(s.method.toUpperCase(),s.url,!0),f.timeout=s.timeout;function x(){if(!f)return;const R=v.from("getAllResponseHeaders"in f&&f.getAllResponseHeaders()),L={data:!c||c==="text"||c==="json"?f.responseText:f.response,status:f.status,statusText:f.statusText,headers:R,config:e,request:f};dt(function(T){n(T),y()},function(T){r(T),y()},L),f=null}"onloadend"in f?f.onloadend=x:f.onreadystatechange=function(){!f||f.readyState!==4||f.status===0&&!(f.responseURL&&f.responseURL.indexOf("file:")===0)||setTimeout(x)},f.onabort=function(){f&&(r(new g("Request aborted",g.ECONNABORTED,e,f)),f=null)},f.onerror=function(A){const L=A&&A.message?A.message:"Network Error",U=new g(L,g.ERR_NETWORK,e,f);U.event=A||null,r(U),f=null},f.ontimeout=function(){let A=s.timeout?"timeout of "+s.timeout+"ms exceeded":"timeout exceeded";const L=s.transitional||Se;s.timeoutErrorMessage&&(A=s.timeoutErrorMessage),r(new g(A,L.clarifyTimeoutError?g.ETIMEDOUT:g.ECONNABORTED,e,f)),f=null},o===void 0&&i.setContentType(null),"setRequestHeader"in f&&a.forEach(i.toJSON(),function(A,L){f.setRequestHeader(L,A)}),a.isUndefined(s.withCredentials)||(f.withCredentials=!!s.withCredentials),c&&c!=="json"&&(f.responseType=s.responseType),d&&([w,u]=se(d,!0),f.addEventListener("progress",w)),p&&f.upload&&([m,E]=se(p),f.upload.addEventListener("progress",m),f.upload.addEventListener("loadend",E)),(s.cancelToken||s.signal)&&(l=R=>{f&&(r(!R||R.type?new X(null,e,f):R),f.abort(),f=null)},s.cancelToken&&s.cancelToken.subscribe(l),s.signal&&(s.signal.aborted?l():s.signal.addEventListener("abort",l)));const C=_n(s.url);if(C&&S.protocols.indexOf(C)===-1){r(new g("Unsupported protocol "+C+":",g.ERR_BAD_REQUEST,e));return}f.send(o||null)})},qn=(e,t)=>{const{length:n}=e=e?e.filter(Boolean):[];if(t||n){let r=new AbortController,s;const o=function(d){if(!s){s=!0,c();const l=d instanceof Error?d:this.reason;r.abort(l instanceof g?l:new X(l instanceof Error?l.message:l))}};let i=t&&setTimeout(()=>{i=null,o(new g(`timeout of ${t}ms exceeded`,g.ETIMEDOUT))},t);const c=()=>{e&&(i&&clearTimeout(i),i=null,e.forEach(d=>{d.unsubscribe?d.unsubscribe(o):d.removeEventListener("abort",o)}),e=null)};e.forEach(d=>d.addEventListener("abort",o));const{signal:p}=r;return p.unsubscribe=()=>a.asap(c),p}},Vn=function*(e,t){let n=e.byteLength;if(n<t){yield e;return}let r=0,s;for(;r<n;)s=r+t,yield e.slice(r,s),r=s},zn=async function*(e,t){for await(const n of $n(e))yield*Vn(n,t)},$n=async function*(e){if(e[Symbol.asyncIterator]){yield*e;return}const t=e.getReader();try{for(;;){const{done:n,value:r}=await t.read();if(n)break;yield r}}finally{await t.cancel()}},Ie=(e,t,n,r)=>{const s=zn(e,t);let o=0,i,c=p=>{i||(i=!0,r&&r(p))};return new ReadableStream({async pull(p){try{const{done:d,value:l}=await s.next();if(d){c(),p.close();return}let m=l.byteLength;if(n){let w=o+=m;n(w)}p.enqueue(new Uint8Array(l))}catch(d){throw c(d),d}},cancel(p){return c(p),s.return()}},{highWaterMark:2})},qe=64*1024,{isFunction:ee}=a,Jn=(({Request:e,Response:t})=>({Request:e,Response:t}))(a.global),{ReadableStream:Ve,TextEncoder:ze}=a.global,$e=(e,...t)=>{try{return!!e(...t)}catch{return!1}},Wn=e=>{e=a.merge.call({skipUndefined:!0},Jn,e);const{fetch:t,Request:n,Response:r}=e,s=t?ee(t):typeof fetch=="function",o=ee(n),i=ee(r);if(!s)return!1;const c=s&&ee(Ve),p=s&&(typeof ze=="function"?(u=>y=>u.encode(y))(new ze):async u=>new Uint8Array(await new n(u).arrayBuffer())),d=o&&c&&$e(()=>{let u=!1;const y=new Ve,f=new n(S.origin,{body:y,method:"POST",get duplex(){return u=!0,"half"}}).headers.has("Content-Type");return y.cancel(),u&&!f}),l=i&&c&&$e(()=>a.isReadableStream(new r("").body)),m={stream:l&&(u=>u.body)};s&&["text","arrayBuffer","blob","formData","stream"].forEach(u=>{!m[u]&&(m[u]=(y,f)=>{let x=y&&y[u];if(x)return x.call(y);throw new g(`Response type '${u}' is not supported`,g.ERR_NOT_SUPPORT,f)})});const w=async u=>{if(u==null)return 0;if(a.isBlob(u))return u.size;if(a.isSpecCompliantForm(u))return(await new n(S.origin,{method:"POST",body:u}).arrayBuffer()).byteLength;if(a.isArrayBufferView(u)||a.isArrayBuffer(u))return u.byteLength;if(a.isURLSearchParams(u)&&(u=u+""),a.isString(u))return(await p(u)).byteLength},E=async(u,y)=>{const f=a.toFiniteNumber(u.getContentLength());return f??w(y)};return async u=>{let{url:y,method:f,data:x,signal:C,cancelToken:R,timeout:A,onDownloadProgress:L,onUploadProgress:U,responseType:T,headers:ue,withCredentials:G="same-origin",fetchOptions:Oe}=ft(u),ve=t||fetch;T=T?(T+"").toLowerCase():"text";let Q=qn([C,R&&R.toAbortSignal()],A),V=null;const B=Q&&Q.unsubscribe&&(()=>{Q.unsubscribe()});let Ce;try{if(U&&d&&f!=="get"&&f!=="head"&&(Ce=await E(ue,x))!==0){let F=new n(y,{method:"POST",body:x,duplex:"half"}),j;if(a.isFormData(x)&&(j=F.headers.get("content-type"))&&ue.setContentType(j),F.body){const[de,Y]=ke(Ce,se(He(U)));x=Ie(F.body,qe,de,Y)}}a.isString(G)||(G=G?"include":"omit");const M=o&&"credentials"in n.prototype,Te={...Oe,signal:Q,method:f.toUpperCase(),headers:ue.normalize().toJSON(),body:x,duplex:"half",credentials:M?G:void 0};V=o&&new n(y,Te);let _=await(o?ve(V,Oe):ve(y,Te));const Ne=l&&(T==="stream"||T==="response");if(l&&(L||Ne&&B)){const F={};["status","statusText","headers"].forEach(Le=>{F[Le]=_[Le]});const j=a.toFiniteNumber(_.headers.get("content-length")),[de,Y]=L&&ke(j,se(He(L),!0))||[];_=new r(Ie(_.body,qe,de,()=>{Y&&Y(),B&&B()}),F)}T=T||"text";let Et=await m[a.findKey(m,T)||"text"](_,u);return!Ne&&B&&B(),await new Promise((F,j)=>{dt(F,j,{data:Et,headers:v.from(_.headers),status:_.status,statusText:_.statusText,config:u,request:V})})}catch(M){throw B&&B(),M&&M.name==="TypeError"&&/Load failed|fetch/i.test(M.message)?Object.assign(new g("Network Error",g.ERR_NETWORK,u,V,M&&M.response),{cause:M.cause||M}):g.from(M,M&&M.code,u,V,M&&M.response)}}},Kn=new Map,pt=e=>{let t=e&&e.env||{};const{fetch:n,Request:r,Response:s}=t,o=[r,s,n];let i=o.length,c=i,p,d,l=Kn;for(;c--;)p=o[c],d=l.get(p),d===void 0&&l.set(p,d=c?new Map:Wn(t)),l=d;return d};pt();const Me={http:dn,xhr:In,fetch:{get:pt}};a.forEach(Me,(e,t)=>{if(e){try{Object.defineProperty(e,"name",{value:t})}catch{}Object.defineProperty(e,"adapterName",{value:t})}});const Je=e=>`- ${e}`,Xn=e=>a.isFunction(e)||e===null||e===!1;function Zn(e,t){e=a.isArray(e)?e:[e];const{length:n}=e;let r,s;const o={};for(let i=0;i<n;i++){r=e[i];let c;if(s=r,!Xn(r)&&(s=Me[(c=String(r)).toLowerCase()],s===void 0))throw new g(`Unknown adapter '${c}'`);if(s&&(a.isFunction(s)||(s=s.get(t))))break;o[c||"#"+i]=s}if(!s){const i=Object.entries(o).map(([p,d])=>`adapter ${p} `+(d===!1?"is not supported by the environment":"is not available in the build"));let c=n?i.length>1?`since :
`+i.map(Je).join(`
`):" "+Je(i[0]):"as no adapter specified";throw new g("There is no suitable adapter to dispatch the request "+c,"ERR_NOT_SUPPORT")}return s}const mt={getAdapter:Zn,adapters:Me};function me(e){if(e.cancelToken&&e.cancelToken.throwIfRequested(),e.signal&&e.signal.aborted)throw new X(null,e)}function We(e){return me(e),e.headers=v.from(e.headers),e.data=pe.call(e,e.transformRequest),["post","put","patch"].indexOf(e.method)!==-1&&e.headers.setContentType("application/x-www-form-urlencoded",!1),mt.getAdapter(e.adapter||K.adapter,e)(e).then(function(r){return me(e),r.data=pe.call(e,e.transformResponse,r),r.headers=v.from(r.headers),r},function(r){return ut(r)||(me(e),r&&r.response&&(r.response.data=pe.call(e,e.transformResponse,r.response),r.response.headers=v.from(r.response.headers))),Promise.reject(r)})}const yt="1.14.0",le={};["object","boolean","number","function","string","symbol"].forEach((e,t)=>{le[e]=function(r){return typeof r===e||"a"+(t<1?"n ":" ")+e}});const Ke={};le.transitional=function(t,n,r){function s(o,i){return"[Axios v"+yt+"] Transitional option '"+o+"'"+i+(r?". "+r:"")}return(o,i,c)=>{if(t===!1)throw new g(s(i," has been removed"+(n?" in "+n:"")),g.ERR_DEPRECATED);return n&&!Ke[i]&&(Ke[i]=!0,console.warn(s(i," has been deprecated since v"+n+" and will be removed in the near future"))),t?t(o,i,c):!0}};le.spelling=function(t){return(n,r)=>(console.warn(`${r} is likely a misspelling of ${t}`),!0)};function Gn(e,t,n){if(typeof e!="object")throw new g("options must be an object",g.ERR_BAD_OPTION_VALUE);const r=Object.keys(e);let s=r.length;for(;s-- >0;){const o=r[s],i=t[o];if(i){const c=e[o],p=c===void 0||i(c,o,e);if(p!==!0)throw new g("option "+o+" must be "+p,g.ERR_BAD_OPTION_VALUE);continue}if(n!==!0)throw new g("Unknown option "+o,g.ERR_BAD_OPTION)}}const re={assertOptions:Gn,validators:le},N=re.validators;let k=class{constructor(t){this.defaults=t||{},this.interceptors={request:new Be,response:new Be}}async request(t,n){try{return await this._request(t,n)}catch(r){if(r instanceof Error){let s={};Error.captureStackTrace?Error.captureStackTrace(s):s=new Error;const o=s.stack?s.stack.replace(/^.+\n/,""):"";try{r.stack?o&&!String(r.stack).endsWith(o.replace(/^.+\n.+\n/,""))&&(r.stack+=`
`+o):r.stack=o}catch{}}throw r}}_request(t,n){typeof t=="string"?(n=n||{},n.url=t):n=t||{},n=H(this.defaults,n);const{transitional:r,paramsSerializer:s,headers:o}=n;r!==void 0&&re.assertOptions(r,{silentJSONParsing:N.transitional(N.boolean),forcedJSONParsing:N.transitional(N.boolean),clarifyTimeoutError:N.transitional(N.boolean),legacyInterceptorReqResOrdering:N.transitional(N.boolean)},!1),s!=null&&(a.isFunction(s)?n.paramsSerializer={serialize:s}:re.assertOptions(s,{encode:N.function,serialize:N.function},!0)),n.allowAbsoluteUrls!==void 0||(this.defaults.allowAbsoluteUrls!==void 0?n.allowAbsoluteUrls=this.defaults.allowAbsoluteUrls:n.allowAbsoluteUrls=!0),re.assertOptions(n,{baseUrl:N.spelling("baseURL"),withXsrfToken:N.spelling("withXSRFToken")},!0),n.method=(n.method||this.defaults.method||"get").toLowerCase();let i=o&&a.merge(o.common,o[n.method]);o&&a.forEach(["delete","get","head","post","put","patch","common"],u=>{delete o[u]}),n.headers=v.concat(i,o);const c=[];let p=!0;this.interceptors.request.forEach(function(y){if(typeof y.runWhen=="function"&&y.runWhen(n)===!1)return;p=p&&y.synchronous;const f=n.transitional||Se;f&&f.legacyInterceptorReqResOrdering?c.unshift(y.fulfilled,y.rejected):c.push(y.fulfilled,y.rejected)});const d=[];this.interceptors.response.forEach(function(y){d.push(y.fulfilled,y.rejected)});let l,m=0,w;if(!p){const u=[We.bind(this),void 0];for(u.unshift(...c),u.push(...d),w=u.length,l=Promise.resolve(n);m<w;)l=l.then(u[m++],u[m++]);return l}w=c.length;let E=n;for(;m<w;){const u=c[m++],y=c[m++];try{E=u(E)}catch(f){y.call(this,f);break}}try{l=We.call(this,E)}catch(u){return Promise.reject(u)}for(m=0,w=d.length;m<w;)l=l.then(d[m++],d[m++]);return l}getUri(t){t=H(this.defaults,t);const n=ht(t.baseURL,t.url,t.allowAbsoluteUrls);return ct(n,t.params,t.paramsSerializer)}};a.forEach(["delete","get","head","options"],function(t){k.prototype[t]=function(n,r){return this.request(H(r||{},{method:t,url:n,data:(r||{}).data}))}});a.forEach(["post","put","patch"],function(t){function n(r){return function(o,i,c){return this.request(H(c||{},{method:t,headers:r?{"Content-Type":"multipart/form-data"}:{},url:o,data:i}))}}k.prototype[t]=n(),k.prototype[t+"Form"]=n(!0)});let Qn=class gt{constructor(t){if(typeof t!="function")throw new TypeError("executor must be a function.");let n;this.promise=new Promise(function(o){n=o});const r=this;this.promise.then(s=>{if(!r._listeners)return;let o=r._listeners.length;for(;o-- >0;)r._listeners[o](s);r._listeners=null}),this.promise.then=s=>{let o;const i=new Promise(c=>{r.subscribe(c),o=c}).then(s);return i.cancel=function(){r.unsubscribe(o)},i},t(function(o,i,c){r.reason||(r.reason=new X(o,i,c),n(r.reason))})}throwIfRequested(){if(this.reason)throw this.reason}subscribe(t){if(this.reason){t(this.reason);return}this._listeners?this._listeners.push(t):this._listeners=[t]}unsubscribe(t){if(!this._listeners)return;const n=this._listeners.indexOf(t);n!==-1&&this._listeners.splice(n,1)}toAbortSignal(){const t=new AbortController,n=r=>{t.abort(r)};return this.subscribe(n),t.signal.unsubscribe=()=>this.unsubscribe(n),t.signal}static source(){let t;return{token:new gt(function(s){t=s}),cancel:t}}};function Yn(e){return function(n){return e.apply(null,n)}}function er(e){return a.isObject(e)&&e.isAxiosError===!0}const be={Continue:100,SwitchingProtocols:101,Processing:102,EarlyHints:103,Ok:200,Created:201,Accepted:202,NonAuthoritativeInformation:203,NoContent:204,ResetContent:205,PartialContent:206,MultiStatus:207,AlreadyReported:208,ImUsed:226,MultipleChoices:300,MovedPermanently:301,Found:302,SeeOther:303,NotModified:304,UseProxy:305,Unused:306,TemporaryRedirect:307,PermanentRedirect:308,BadRequest:400,Unauthorized:401,PaymentRequired:402,Forbidden:403,NotFound:404,MethodNotAllowed:405,NotAcceptable:406,ProxyAuthenticationRequired:407,RequestTimeout:408,Conflict:409,Gone:410,LengthRequired:411,PreconditionFailed:412,PayloadTooLarge:413,UriTooLong:414,UnsupportedMediaType:415,RangeNotSatisfiable:416,ExpectationFailed:417,ImATeapot:418,MisdirectedRequest:421,UnprocessableEntity:422,Locked:423,FailedDependency:424,TooEarly:425,UpgradeRequired:426,PreconditionRequired:428,TooManyRequests:429,RequestHeaderFieldsTooLarge:431,UnavailableForLegalReasons:451,InternalServerError:500,NotImplemented:501,BadGateway:502,ServiceUnavailable:503,GatewayTimeout:504,HttpVersionNotSupported:505,VariantAlsoNegotiates:506,InsufficientStorage:507,LoopDetected:508,NotExtended:510,NetworkAuthenticationRequired:511,WebServerIsDown:521,ConnectionTimedOut:522,OriginIsUnreachable:523,TimeoutOccurred:524,SslHandshakeFailed:525,InvalidSslCertificate:526};Object.entries(be).forEach(([e,t])=>{be[t]=e});function wt(e){const t=new k(e),n=Ge(k.prototype.request,t);return a.extend(n,k.prototype,t,{allOwnKeys:!0}),a.extend(n,t,null,{allOwnKeys:!0}),n.create=function(s){return wt(H(e,s))},n}const b=wt(K);b.Axios=k;b.CanceledError=X;b.CancelToken=Qn;b.isCancel=ut;b.VERSION=yt;b.toFormData=ce;b.AxiosError=g;b.Cancel=b.CanceledError;b.all=function(t){return Promise.all(t)};b.spread=Yn;b.isAxiosError=er;b.mergeConfig=H;b.AxiosHeaders=v;b.formToJSON=e=>lt(a.isHTMLForm(e)?new FormData(e):e);b.getAdapter=mt.getAdapter;b.HttpStatusCode=be;b.default=b;const{Axios:Ts,AxiosError:Ns,CanceledError:Ls,isCancel:Ps,CancelToken:_s,VERSION:Fs,all:Us,Cancel:Bs,isAxiosError:Ds,spread:ks,toFormData:Hs,AxiosHeaders:js,HttpStatusCode:Is,formToJSON:qs,getAdapter:Vs,mergeConfig:zs}=b;window.axios=b;window.axios.defaults.headers.common["X-Requested-With"]="XMLHttpRequest";/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const bt=(e,t,n=[])=>{const r=document.createElementNS("http://www.w3.org/2000/svg",e);return Object.keys(t).forEach(s=>{r.setAttribute(s,String(t[s]))}),n.length&&n.forEach(s=>{const o=bt(...s);r.appendChild(o)}),r};var tr=([e,t,n])=>bt(e,t,n);/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const nr=e=>Array.from(e.attributes).reduce((t,n)=>(t[n.name]=n.value,t),{}),rr=e=>typeof e=="string"?e:!e||!e.class?"":e.class&&typeof e.class=="string"?e.class.split(" "):e.class&&Array.isArray(e.class)?e.class:"",sr=e=>e.flatMap(rr).map(n=>n.trim()).filter(Boolean).filter((n,r,s)=>s.indexOf(n)===r).join(" "),or=e=>e.replace(/(\w)(\w*)(_|-|\s*)/g,(t,n,r)=>n.toUpperCase()+r.toLowerCase()),Xe=(e,{nameAttr:t,icons:n,attrs:r})=>{var u;const s=e.getAttribute(t);if(s==null)return;const o=or(s),i=n[o];if(!i)return console.warn(`${e.outerHTML} icon name was not found in the provided icons object.`);const c=nr(e),[p,d,l]=i,m={...d,"data-lucide":s,...r,...c},w=sr(["lucide",`lucide-${s}`,c,r]);w&&Object.assign(m,{class:w});const E=tr([p,m,l]);return(u=e.parentNode)==null?void 0:u.replaceChild(E,e)};/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const h={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round"};/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ir=["svg",h,[["rect",{width:"20",height:"5",x:"2",y:"3",rx:"1"}],["path",{d:"M4 8v11a2 2 0 0 0 2 2h2"}],["path",{d:"M20 8v11a2 2 0 0 1-2 2h-2"}],["path",{d:"m9 15 3-3 3 3"}],["path",{d:"M12 12v9"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ar=["svg",h,[["rect",{width:"20",height:"5",x:"2",y:"3",rx:"1"}],["path",{d:"M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"}],["path",{d:"M10 12h4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const cr=["svg",h,[["path",{d:"M5 9v6"}],["path",{d:"M9 9h3V5l7 7-7 7v-4H9V9z"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const lr=["svg",h,[["path",{d:"m12 19-7-7 7-7"}],["path",{d:"M19 12H5"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ur=["svg",h,[["path",{d:"M5 12h14"}],["path",{d:"m12 5 7 7-7 7"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const dr=["svg",h,[["path",{d:"M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"}],["path",{d:"M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"}],["path",{d:"M12 18V6"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const hr=["svg",h,[["path",{d:"M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"}],["path",{d:"M10.3 21a1.94 1.94 0 0 0 3.4 0"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const fr=["svg",h,[["path",{d:"M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"}],["path",{d:"m3.3 7 8.7 5 8.7-5"}],["path",{d:"M12 22V12"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const pr=["svg",h,[["path",{d:"M12 12h.01"}],["path",{d:"M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"}],["path",{d:"M22 13a18.15 18.15 0 0 1-20 0"}],["rect",{width:"20",height:"14",x:"2",y:"6",rx:"2"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const mr=["svg",h,[["path",{d:"M8 2v4"}],["path",{d:"M16 2v4"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2"}],["path",{d:"M3 10h18"}],["path",{d:"m9 16 2 2 4-4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const yr=["svg",h,[["path",{d:"M8 2v4"}],["path",{d:"M16 2v4"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2"}],["path",{d:"M3 10h18"}],["path",{d:"M8 14h.01"}],["path",{d:"M12 14h.01"}],["path",{d:"M16 14h.01"}],["path",{d:"M8 18h.01"}],["path",{d:"M12 18h.01"}],["path",{d:"M16 18h.01"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const gr=["svg",h,[["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2"}],["path",{d:"M16 2v4"}],["path",{d:"M3 10h18"}],["path",{d:"M8 2v4"}],["path",{d:"M17 14h-6"}],["path",{d:"M13 18H7"}],["path",{d:"M7 14h.01"}],["path",{d:"M17 18h.01"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const wr=["svg",h,[["path",{d:"M8 2v4"}],["path",{d:"M16 2v4"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2"}],["path",{d:"M3 10h18"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const br=["svg",h,[["path",{d:"M3 3v16a2 2 0 0 0 2 2h16"}],["rect",{x:"15",y:"5",width:"4",height:"12",rx:"1"}],["rect",{x:"7",y:"8",width:"4",height:"9",rx:"1"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const xr=["svg",h,[["path",{d:"M20 6 9 17l-5-5"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Er=["svg",h,[["path",{d:"m6 9 6 6 6-6"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Rr=["svg",h,[["path",{d:"m9 18 6-6-6-6"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Sr=["svg",h,[["path",{d:"m18 15-6-6-6 6"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ar=["svg",h,[["path",{d:"m7 15 5 5 5-5"}],["path",{d:"m7 9 5-5 5 5"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Mr=["svg",h,[["circle",{cx:"12",cy:"12",r:"10"}],["line",{x1:"12",x2:"12",y1:"8",y2:"12"}],["line",{x1:"12",x2:"12.01",y1:"16",y2:"16"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Or=["svg",h,[["path",{d:"M21.801 10A10 10 0 1 1 17 3.335"}],["path",{d:"m9 11 3 3L22 4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const vr=["svg",h,[["circle",{cx:"12",cy:"12",r:"10"}],["path",{d:"m9 12 2 2 4-4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Cr=["svg",h,[["circle",{cx:"12",cy:"12",r:"10"}],["path",{d:"M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"}],["path",{d:"M12 17h.01"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Tr=["svg",h,[["circle",{cx:"12",cy:"12",r:"10"}],["path",{d:"m15 9-6 6"}],["path",{d:"m9 9 6 6"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Nr=["svg",h,[["circle",{cx:"8",cy:"8",r:"6"}],["path",{d:"M18.09 10.37A6 6 0 1 1 10.34 18"}],["path",{d:"M7 6h1v4"}],["path",{d:"m16.71 13.88.7.71-2.82 2.82"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Lr=["svg",h,[["rect",{width:"20",height:"14",x:"2",y:"5",rx:"2"}],["line",{x1:"2",x2:"22",y1:"10",y2:"10"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Pr=["svg",h,[["ellipse",{cx:"12",cy:"5",rx:"9",ry:"3"}],["path",{d:"M3 5V19A9 3 0 0 0 15 21.84"}],["path",{d:"M21 5V8"}],["path",{d:"M21 12L18 17H22L19 22"}],["path",{d:"M3 12A9 3 0 0 0 14.59 14.87"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const _r=["svg",h,[["ellipse",{cx:"12",cy:"5",rx:"9",ry:"3"}],["path",{d:"M3 5V19A9 3 0 0 0 21 19V5"}],["path",{d:"M3 12A9 3 0 0 0 21 12"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Fr=["svg",h,[["path",{d:"M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"}],["polyline",{points:"7 10 12 15 17 10"}],["line",{x1:"12",x2:"12",y1:"15",y2:"3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ur=["svg",h,[["circle",{cx:"12",cy:"12",r:"1"}],["circle",{cx:"12",cy:"5",r:"1"}],["circle",{cx:"12",cy:"19",r:"1"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Br=["svg",h,[["path",{d:"M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"}],["circle",{cx:"12",cy:"12",r:"3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Dr=["svg",h,[["path",{d:"M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"}],["path",{d:"M14 2v4a2 2 0 0 0 2 2h4"}],["path",{d:"M10 9H8"}],["path",{d:"M16 13H8"}],["path",{d:"M16 17H8"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const kr=["svg",h,[["path",{d:"M13.013 3H2l8 9.46V19l4 2v-8.54l.9-1.055"}],["path",{d:"m22 3-5 5"}],["path",{d:"m17 3 5 5"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Hr=["svg",h,[["polygon",{points:"22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const jr=["svg",h,[["circle",{cx:"18",cy:"18",r:"3"}],["path",{d:"M10.3 20H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H20a2 2 0 0 1 2 2v3.3"}],["path",{d:"m21.7 19.4-.9-.3"}],["path",{d:"m15.2 16.9-.9-.3"}],["path",{d:"m16.6 21.7.3-.9"}],["path",{d:"m19.1 15.2.3-.9"}],["path",{d:"m19.6 21.7-.4-1"}],["path",{d:"m16.8 15.3-.4-1"}],["path",{d:"m14.3 19.6 1-.4"}],["path",{d:"m20.7 16.8 1-.4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ir=["svg",h,[["path",{d:"M12 10v6"}],["path",{d:"M9 13h6"}],["path",{d:"M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const qr=["svg",h,[["circle",{cx:"12",cy:"12",r:"10"}],["path",{d:"M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"}],["path",{d:"M2 12h20"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Vr=["svg",h,[["path",{d:"M16 5h6"}],["path",{d:"M19 2v6"}],["path",{d:"M21 11.5V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7.5"}],["path",{d:"m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"}],["circle",{cx:"9",cy:"9",r:"2"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const zr=["svg",h,[["rect",{width:"18",height:"18",x:"3",y:"3",rx:"2",ry:"2"}],["circle",{cx:"9",cy:"9",r:"2"}],["path",{d:"m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const $r=["svg",h,[["polyline",{points:"22 12 16 12 14 15 10 15 8 12 2 12"}],["path",{d:"M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Jr=["svg",h,[["path",{d:"m5 8 6 6"}],["path",{d:"m4 14 6-6 2-3"}],["path",{d:"M2 5h12"}],["path",{d:"M7 2h1"}],["path",{d:"m22 22-5-10-5 10"}],["path",{d:"M14 18h6"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Wr=["svg",h,[["path",{d:"m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"}],["path",{d:"m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"}],["path",{d:"m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Kr=["svg",h,[["rect",{width:"7",height:"7",x:"3",y:"3",rx:"1"}],["rect",{width:"7",height:"7",x:"14",y:"3",rx:"1"}],["rect",{width:"7",height:"7",x:"14",y:"14",rx:"1"}],["rect",{width:"7",height:"7",x:"3",y:"14",rx:"1"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Xr=["svg",h,[["path",{d:"m3 17 2 2 4-4"}],["path",{d:"m3 7 2 2 4-4"}],["path",{d:"M13 6h8"}],["path",{d:"M13 12h8"}],["path",{d:"M13 18h8"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Zr=["svg",h,[["path",{d:"M3 12h.01"}],["path",{d:"M3 18h.01"}],["path",{d:"M3 6h.01"}],["path",{d:"M8 12h13"}],["path",{d:"M8 18h13"}],["path",{d:"M8 6h13"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Gr=["svg",h,[["path",{d:"M21 12a9 9 0 1 1-6.219-8.56"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Qr=["svg",h,[["path",{d:"M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"}],["polyline",{points:"16 17 21 12 16 7"}],["line",{x1:"21",x2:"9",y1:"12",y2:"12"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Yr=["svg",h,[["line",{x1:"4",x2:"20",y1:"12",y2:"12"}],["line",{x1:"4",x2:"20",y1:"6",y2:"6"}],["line",{x1:"4",x2:"20",y1:"18",y2:"18"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const es=["svg",h,[["path",{d:"M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ts=["svg",h,[["rect",{width:"18",height:"18",x:"3",y:"3",rx:"2"}],["path",{d:"M15 3v18"}],["path",{d:"m10 15-3-3 3-3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ns=["svg",h,[["rect",{width:"18",height:"18",x:"3",y:"3",rx:"2"}],["path",{d:"M3 9h18"}],["path",{d:"m15 14-3 3-3-3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const rs=["svg",h,[["rect",{width:"18",height:"18",x:"3",y:"3",rx:"2"}],["path",{d:"M3 9h18"}],["path",{d:"M9 21V9"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ss=["svg",h,[["path",{d:"M12 20h9"}],["path",{d:"M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const os=["svg",h,[["path",{d:"M5 12h14"}],["path",{d:"M12 5v14"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const is=["svg",h,[["path",{d:"M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"}],["path",{d:"M21 3v5h-5"}],["path",{d:"M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"}],["path",{d:"M8 16H3v5"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const as=["svg",h,[["path",{d:"M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"}],["path",{d:"M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"}],["path",{d:"M7 3v4a1 1 0 0 0 1 1h7"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const cs=["svg",h,[["path",{d:"M3 7V5a2 2 0 0 1 2-2h2"}],["path",{d:"M17 3h2a2 2 0 0 1 2 2v2"}],["path",{d:"M21 17v2a2 2 0 0 1-2 2h-2"}],["path",{d:"M7 21H5a2 2 0 0 1-2-2v-2"}],["path",{d:"M7 12h10"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ls=["svg",h,[["path",{d:"m8 11 2 2 4-4"}],["circle",{cx:"11",cy:"11",r:"8"}],["path",{d:"m21 21-4.3-4.3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const us=["svg",h,[["path",{d:"m13.5 8.5-5 5"}],["path",{d:"m8.5 8.5 5 5"}],["circle",{cx:"11",cy:"11",r:"8"}],["path",{d:"m21 21-4.3-4.3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ds=["svg",h,[["circle",{cx:"11",cy:"11",r:"8"}],["path",{d:"m21 21-4.3-4.3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const hs=["svg",h,[["path",{d:"M20 7h-9"}],["path",{d:"M14 17H5"}],["circle",{cx:"17",cy:"17",r:"3"}],["circle",{cx:"7",cy:"7",r:"3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const fs=["svg",h,[["path",{d:"M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"}],["circle",{cx:"12",cy:"12",r:"3"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ps=["svg",h,[["path",{d:"M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"}],["path",{d:"m9 12 2 2 4-4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ms=["svg",h,[["line",{x1:"21",x2:"14",y1:"4",y2:"4"}],["line",{x1:"10",x2:"3",y1:"4",y2:"4"}],["line",{x1:"21",x2:"12",y1:"12",y2:"12"}],["line",{x1:"8",x2:"3",y1:"12",y2:"12"}],["line",{x1:"21",x2:"16",y1:"20",y2:"20"}],["line",{x1:"12",x2:"3",y1:"20",y2:"20"}],["line",{x1:"14",x2:"14",y1:"2",y2:"6"}],["line",{x1:"8",x2:"8",y1:"10",y2:"14"}],["line",{x1:"16",x2:"16",y1:"18",y2:"22"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ys=["svg",h,[["line",{x1:"4",x2:"4",y1:"21",y2:"14"}],["line",{x1:"4",x2:"4",y1:"10",y2:"3"}],["line",{x1:"12",x2:"12",y1:"21",y2:"12"}],["line",{x1:"12",x2:"12",y1:"8",y2:"3"}],["line",{x1:"20",x2:"20",y1:"21",y2:"16"}],["line",{x1:"20",x2:"20",y1:"12",y2:"3"}],["line",{x1:"2",x2:"6",y1:"14",y2:"14"}],["line",{x1:"10",x2:"14",y1:"8",y2:"8"}],["line",{x1:"18",x2:"22",y1:"16",y2:"16"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ze=["svg",h,[["path",{d:"M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"}],["path",{d:"M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const gs=["svg",h,[["path",{d:"M12 8a2.83 2.83 0 0 0 4 4 4 4 0 1 1-4-4"}],["path",{d:"M12 2v2"}],["path",{d:"M12 20v2"}],["path",{d:"m4.9 4.9 1.4 1.4"}],["path",{d:"m17.7 17.7 1.4 1.4"}],["path",{d:"M2 12h2"}],["path",{d:"M20 12h2"}],["path",{d:"m6.3 17.7-1.4 1.4"}],["path",{d:"m19.1 4.9-1.4 1.4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ws=["svg",h,[["circle",{cx:"12",cy:"12",r:"4"}],["path",{d:"M12 2v2"}],["path",{d:"M12 20v2"}],["path",{d:"m4.93 4.93 1.41 1.41"}],["path",{d:"m17.66 17.66 1.41 1.41"}],["path",{d:"M2 12h2"}],["path",{d:"M20 12h2"}],["path",{d:"m6.34 17.66-1.41 1.41"}],["path",{d:"m19.07 4.93-1.41 1.41"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const bs=["svg",h,[["path",{d:"M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"}],["circle",{cx:"7.5",cy:"7.5",r:".5",fill:"currentColor"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const xs=["svg",h,[["path",{d:"M3 6h18"}],["path",{d:"M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"}],["path",{d:"M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"}],["line",{x1:"10",x2:"10",y1:"11",y2:"17"}],["line",{x1:"14",x2:"14",y1:"11",y2:"17"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Es=["svg",h,[["path",{d:"M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"}],["circle",{cx:"12",cy:"7",r:"4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Rs=["svg",h,[["path",{d:"M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"}],["circle",{cx:"9",cy:"7",r:"4"}],["path",{d:"M22 21v-2a4 4 0 0 0-3-3.87"}],["path",{d:"M16 3.13a4 4 0 0 1 0 7.75"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ss=["svg",h,[["path",{d:"M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"}],["path",{d:"M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const As=["svg",h,[["path",{d:"M18 6 6 18"}],["path",{d:"m6 6 12 12"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ms=["svg",h,[["path",{d:"M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"}]]];/**
 * @license lucide v0.453.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const xe=({icons:e={},nameAttr:t="data-lucide",attrs:n={}}={})=>{if(!Object.values(e).length)throw new Error(`Please provide an icons object.
If you want to use all the icons you can import it like:
 \`import { createIcons, icons } from 'lucide';
lucide.createIcons({icons});\``);if(typeof document>"u")throw new Error("`createIcons()` only works in a browser environment.");const r=document.querySelectorAll(`[${t}]`);if(Array.from(r).forEach(s=>Xe(s,{nameAttr:t,icons:e,attrs:n})),t==="data-lucide"){const s=document.querySelectorAll("[icon-name]");s.length>0&&(console.warn("[Lucide] Some icons were found with the now deprecated icon-name attribute. These will still be replaced for backwards compatibility, but will no longer be supported in v1.0 and you should switch to data-lucide"),Array.from(s).forEach(o=>Xe(o,{nameAttr:"icon-name",icons:e,attrs:n})))}},xt={Menu:Yr,X:As,Bell:hr,User:Es,Settings:fs,LogOut:Qr,ChevronRight:Rr,Search:ds,Plus:os,Filter:Hr,Download:Fr,Trash2:xs,Edit:Ze,Check:xr,Eye:Br,Save:as,XCircle:Tr,MoreVertical:Ur,LayoutGrid:Kr,List:Zr,Sliders:ys,Calendar:wr,Moon:es,Sun:ws,Globe:qr,Database:_r,CreditCard:Lr,Box:fr,Archive:ar,FileText:Dr,Image:zr,RefreshCw:is,Layers:Wr,CheckCircle2:vr,Coins:Nr,Languages:Jr,SunMoon:gs,ArrowRight:ur,PanelTopOpen:ns,ShieldCheck:ps,DatabaseZap:Pr,Loader2:Gr,SquarePen:Ze,FilterX:kr,SearchCheck:ls,SlidersHorizontal:ms,ChevronDown:Er,FolderCog:jr,FolderPlus:Ir,ArrowLeft:lr,ImagePlus:Vr,ArchiveRestore:ir,PanelRightOpen:ts,ChartColumnBig:br,BriefcaseBusiness:pr,Users:Rs,BadgeDollarSign:dr,HelpCircle:Cr,ScanLine:cs,PanelsTopLeft:rs,Settings2:hs,ListChecks:Xr,Wallet:Ss,ArrowBigRightDash:cr,Edit3:ss,ChevronsUpDown:Ar,Zap:Ms,AlertCircle:Mr,CheckCircle:Or,Inbox:$r,CalendarDays:yr,CalendarRange:gr,CalendarCheck:mr,ChevronUp:Sr,Tag:bs,SearchX:us},Z=()=>{try{typeof xe=="function"&&xe({icons:xt})}catch{}};window.initializeIcons=Z;window.lucide={createIcons:xe,icons:xt};document.addEventListener("DOMContentLoaded",Z);document.addEventListener("livewire:navigated",Z);window.addEventListener("load",Z);document.addEventListener("livewire:initialized",()=>{Livewire.hook("morph.updated",(e,t)=>{Z()})});
