!function(b){var o,i=0,p=[],u=[],l=[],c=[],h=0,d=0;function T(t,n,a,e){var s,r;n=b("<div>"+b.trim(n)+"</div>").text(),s=n,r=a,d+=1,b("#progress_bar").progressbar("value",d/h*100),b("#p").text("("+r+") "+s),d===h&&b("#tr_loading").data("done",!0),clearTimeout(o),i+=1,u.push(t),p.push(n),l.push(a),c.push(e),o=setTimeout(function(){var t,n={action:"tp_translation",items:i};for(t=0;t<i;t+=1)u[t]!==u[t-1]&&(n["tk"+t]=u[t]),l[t]!==l[t-1]&&(n["ln"+t]=l[t]),p[t]!==p[t-1]&&(n["tr"+t]=p[t]),c[t]!==c[t-1]&&(n["sr"+t]=c[t]);b.ajax({type:"POST",url:t_jp.ajaxurl,data:n,success:function(){},error:function(){}}),i=0,p=[],u=[],l=[],c=[]},200)}function f(v,j,x){t_jp.preferred.some(function(t){if(-1!==t_be[t+"_langs"].indexOf(x))return"a"===t&&(f=v,_=j,g=x,t_jp.dat(_,function(t){200<=t.responseStatus&&t.responseStatus<300&&(void 0!==t.responseData.translatedText?T(f[0],t.responseData.translatedText):b(t.responseData).each(function(t){200===this.responseStatus&&T(f[t],this.responseData.translatedText,g,3)}))},g)),"b"===t&&(l=v,c=j,"zh"===(d=h=x)?d="zh-chs":"zh-tw"===d&&(d="zh-cht"),t_jp.dbt(c,function(t){b(t).each(function(t){T(l[t],this.TranslatedText,h,2)})},d)),"g"===t&&(i=v,p=j,u=x,t_jp.dgpt(p,function(t){b(t.results).each(function(t){T(i[t],this,u,1)})},u)),"y"===t&&(s=v,r=j,o=x,t_jp.dyt(r,function(t){b(t.results).each(function(t){T(s[t],this,o,4)})},o)),"u"===t&&(n=v,a=j,e=x,t_jp.dut(a,function(t){b(t.results).each(function(t){T(n[t],this,e,4)})},e)),!0;var n,a,e,s,r,o,i,p,u,l,c,h,d,f,_,g})}function t(t){var a,e,s,r,o="",i=[],p=[],u=0,l=[],c=[];b("#tr_loading").data("done",!1),b.ajax({url:ajaxurl,dataType:"json",data:{action:"tp_post_phrases",post:t},cache:!1,success:function(t){if(b("#tr_translate_title").html("Translating post: "+t.posttitle),void 0===t.length)return b("#tr_loading").html("Nothing left to translate"),void b("#tr_loading").data("done",!0);for(e in d=h=0,t.p)h+=t.p[e].l.length;for(var n in b("#tr_loading").html('<br/>Translation: <span id="p"></span><div id="progress_bar"/>'),b("#progress_bar").progressbar({value:0}),t.langs){for(e in o=t.langs[n],p=[],i=[],t.p)-1!==(s=t.p[e]).l.indexOf(o)&&(p.push(unescape(e)),i.push(unescape(e)),s.l.splice(s.l.indexOf(o),1),0===s.l.length&&(t.length-=1,delete t.p[e]));if(p.length){for(a in p)r=p[a],512<u+r.length&&(f(c,l,o),u=0,l=[],c=[]),u+=r.length,c.push(i[a]),l.push(r);f(c,l,o)}}}})}window.translate_post=t,b(function(){t_be.post&&t(t_be.post)})}(jQuery);
//# sourceMappingURL=backendtranslate.js.map