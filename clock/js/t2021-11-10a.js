//ⓒ Copyright Time.is AS
year_offset=0
function l0(n){return n>9?n:"0"+n*1}
function sppl(AA,AB){AA=AA+''
AB=AB.replace('%n',AA.replace('.',p_dec_sym))
var AC=AB.indexOf('[')
if(AC==-1)return AB
var AD=AA.slice(-1),AE=AB.substr(AC+2,AB.indexOf(']')-AC-2).split('|'),AF=AB.slice(0,AC)
if(l=='cs'){if(AA!=0&&AA<5)return AF+AE[0];return AF+AE[1]}
if(l=='mk'){if(AA%10==1&&AA%100!=11)return AF+AE[1];return AF+AE[0]}
if(l=='ru'&&-1!=AA.indexOf('.'))return AF+AE[2]
if((4<AA&&AA<21)||'056789'.indexOf(AD)!=-1)return AF+AE[0]
if(1==AD)return AF+AE[1]
return AF+AE[2]}
function nicetime(AG,AH){var AI='',AJ=Math.floor(AG/86400),AK=AG%86400
if(AJ==1)AI+='1 '+p_d
else if(AJ!=0)AI+=sppl(AJ,"%n"+p_ds)
AJ=Math.floor(AK/3600)
if(AJ!=0){if(AI!='')AI+=', '
if(AJ==1)AI+='1'+p_h
else if(AJ!=0)AI+=sppl(AJ,"%n"+p_hs)}
AK=AK%3600
AJ=Math.floor(AK/60)
if(AJ!=0){if(AI!='')AI+=', '
if(AJ==1)AI+='1'+p_m
else if(AJ!=0)AI+=sppl(AJ,"%n"+p_ms)}
if(AI!='')AI+=p_and
AJ=(AK%60).toFixed(AH)
if(AH==0&&AJ==1)AI+='1'+p_s;else AI+=sppl(AJ,"%n"+p_ss)
return AI}
function format_time(t,d,of,AL){if(typeof of===U)of=0
var t2=new Date(t.getTime()+of*60000),f=conf['t'],H=t2.getUTCHours(),h=H,mins=t2.getUTCMinutes(),s=t2.getUTCSeconds(),m=l0(t2.getUTCMilliseconds()),a='am',IT=(H*3600+mins*60+s)/86.4
m=m>99?m:"0"+m
if(11<h){a='pm';h-=12}
if(h==0)h=12
if(d===0||d===F)f=f.replace(/:?\.?s[,\.]?M?T?/,'')
if(AL===1)f=f.replace('h','H').replace('A','')
return f.replace('g',h).replace('h',l0(h)).replace('H',l0(H)).replace('i',l0(mins)).replace('s',l0(s)).replace('M',m).replace('A',a).replace('@',IT)}
function build_date(AM,AN){var AO=AM.getUTCFullYear()+'',AP=AM.getUTCMonth()+1
var AQ=new Date(Date.UTC(AO,0,1)),AR=AQ.getUTCDay()-1
if(AR==-1)AR=6
var AS=Math.floor((AM-AQ)/604800000+(AR/7)),AT=p_d+' '+Math.floor((AM-AQ)/86400000+1)
if(AR<4)AS++
if(AS==0){AS=52
if(new Date(AO-1,0,1).getDay()==4||new Date(AO-1,11,31).getDay()==4)AS=53}
if((AS==52||AS==53)&&AM.getUTCDay()!=0&&27<AM.getUTCDate()-AM.getUTCDay())AS=1
if(p_wn=='hy'){if(AS==1)pwn='1-ին շաբաթ';else pwn=AS+'-րդ շաբաթ'}else pwn=p_wn.replace('%n',AS)
AN=AN.replace('%j',AM.getUTCDate()).replace('%d',l0(AM.getUTCDate())).replace('%W','<span class="nw">'+pwn+'</span>').replace('%w',AS).replace('%n',AP).replace('%m',l0(AP)).replace('%y',(AO*1+year_offset+'').substr(2,2)).replace('%Y',AO*1+year_offset).replace('%F',months[AP-1]).replace('%M',monthsh[AP-1]).replace('%l',days[AM.getUTCDay()]).replace('%D',daysh[AM.getUTCDay()]).replace('%z',AT)
if(l=='ca')AN=AN.replace(/ de (A|O)/,' d\'$1')
return AN}
function sh_d(AM){var AO=AM.getUTCFullYear()+'',AP=AM.getUTCMonth()+1
return df2.replace('%j',AM.getUTCDate()).replace('%n',AP).replace('%y',AO.substr(2,2)).replace('%Y',AO).replace('%F',months[AP-1]).replace('%M',monthsh[AP-1]).replace('%d',l0(AM.getUTCDate())).replace('%m',l0(AP))}
function TimeIs(AU,AV,AW,AX,AY){var	AZ=this,AU=gob(AU),AV=gob(AV),AW=gob(AW),AX=gob(AX),BA=0,BB=0,BC=0,BD=0,BF=0
AZ.susdivo=gob('susdiv')
AZ.susdivname='susdiv'
AZ.timeout=''
this.initClock=function(){T=new Date()
var czo=-T.getTimezoneOffset(),AG=Math.abs(tD/1000).toFixed(1),BG='+',BH,BI,BJ=p_ss
if(ww<241)BJ=p_ss_short
if(conf['o']==='')conf['o']=czo
T.setTime(T.getTime()-tD)
BF=ticks
if(AZ.timeout!='')clearTimeout(AZ.timeout)
AZ.timeout=setTimeout('T_I.tick("",1)',updint-T%updint+5)
AZ.tick('',0)
if(tD>0)BG=''
BK=p_time_diff
if(ww<241)BK=p_time_diff_short
if(4000<Math.abs(cY)){BH=p_failh
BI=p_failm+BK.replace('%t','<span class="nw">'+BG+sppl((-tD/1000).toFixed(3),"%n"+BJ)+'</span>').replace('%D',sppl((cY/2000).toFixed(3),"%n"+BJ))}else if(AG<.2){BH=p_exactt
BI=BK.replace('%t','<span class="nw">'+BG+sppl((-tD/1000).toFixed(3),"%n"+BJ)+'</span>').replace('%D',sppl((cY/2000).toFixed(3),"%n"+BJ))}else{var AI=nicetime(AG,1),BL=p_acc
if(tD<0)BH=p_ur_late.replace('%t',AI)
else BH=p_ur_early.replace('%t',AI)
if(ww<321)BL=p_acc_short
BI=BL.replace('%t',sppl((cY/2000).toFixed(3),"%n"+p_ss))}
return [BH,BI]}
this.tick=function(id,tack){try{T=new Date()
T.setTime(T.getTime()-tD)
var BM,BN,BO,secs=T.getUTCSeconds()
var nowTS=Math.floor(T.valueOf()/1000)
if(nowTS===leapTS){Ltmpcorr=1;Loffset=-1;prevT=T}
else Ltmpcorr=0
if(nowTS===leapTS&&prevTS===leapTS)Loffset=0
if(Loffset!==0)T.setTime(T.getTime()+Loffset*1000)
prevTS=nowTS
if((nextSyncT!=0&&nextSyncT<T)||T<prevT){prevT=0;syncn=0;rsy=1;httpSync()}
else if(ticks==1)httpSync()
setmsgH()
if(this.prevS!=secs){
if(auSt==1&&3<ticks){if(secs===0){BP=t_au1m
t_au59s.volume=0
t_au1s.volume=0}
else if(secs<58){BP=t_au1s;t_au59s.volume=0;t_au1m.volume=0}
else{BP=t_au59s;t_au1m.volume=0;t_au1s.volume=0}
if(!isNaN(BP.duration)&&(BP.duration!==0)){BP.currentTime=0;BP.volume=1}
BP.play()}
ticks++
this.prevS=secs
}
if(destT!=0){diff=Math.floor(T.getTime()/1000)-destT -Loffset
if(diff<0){var diffmsg=p_time_remaining;diff=Math.abs(diff)}
else var diffmsg=p_time_since
gob('desttdiff').innerHTML=diffmsg.replace('%t',spanwrap_digitz(nicetime(diff,0)))}
BN=new Date(T.getTime())
update_big_clock(BQ,clocks[0],!tack)
if(typeof other_clock!=U)other_clock(T.getUTCHours(),T.getUTCMinutes(),secs,T.getUTCMilliseconds())
if(document.readyState==='complete'&&typeof every_second!=U)every_second(BN)
BN.setTime(BN.getTime()+get_zone_offset(clocks[0].zone_code,BN)*60000)
var AL=0,BO
if(clocks[0].locName.length>2&&clocks[0].locName.substr(0,3)==='UTC')AL=1
BO=format_time(BN,0,0,AL)
if(uT&&Tstate.current_page!=='calendar'){var BR=BO.replace('<span class="ampm">','').replace('</span>','')
if(Tstate.current_page=='Unix_time_now')BR=Math.floor(T.getTime()/1000)
D.title=uT+BR}
if(BC!=BO){BC=BO
if(typeof current_q[T_I.susdivname]==S&&current_q[T_I.susdivname]!=''&&fc[T_I.susdivname]&&gob(T_I.susdivname).innerHTML!='')T_I.populate_sus(T_I.susdivname,current_q[T_I.susdivname],0)
BN=new Date(T.getTime())
var BS=!tack
T_I.update_favs(BN,BS)}}catch(err){}
prevT=T
if(!tack)return ''
var i=updint
if(i>99)i=updint-(new Date().getTime()-tD)%updint+5
AZ.timeout=setTimeout('T_I.tick("",1)',i)}
this.update_favs=function(T,BT){if(0==locs['favs'].length)return ''
var html='',i
for(i in locs['favs']){if(i!=='indexOf'){var AM=new Date(),BU=locs['favs'][i][1],BV,BW,BX,o,AL=0
AM.setTime(T.getTime()+get_zone_offset(BU,T)*60000)
if(locs['favs'][i][9])BX=locs['favs'][i][9]
else BX=locs['favs'][i][2]
if(BX.length>2&&BX.substr(0,3)==='UTC')AL=1
BV='<span class="time">'+spanwrap_digitz(format_time(AM,0,0,AL))+'</span>'
if(BT){BW=''
html+='<li id="favbox-'+i+'"'
html+='><a href="'+locs['favs'][i][4].replace('$1',locs['favs'][i][2])
html+=BW+'" id="time-'+i+'">'+BX+'<br>'+BV+'</a></li>'}else{o=gob('time-'+i)
if(o)o.innerHTML=BX+'<br>'+BV}}}
o=gob('favs')
if(BT&&o)o.innerHTML=html}
this.check_again=function(){if(xR=='N/A')location.reload();else{rsy=1;syncn=0;httpSync()}}
this.recook=function(){var s=X='',i
for(i in conf){s+=X+i+conf[i];X='X'}
confs=s.replace(/ /g,'_').replace(/,/g,'1').replace(/:/g,'2').replace(/%/g,'3')
setcookie('c',confs)}
this.set_susdiv=function(sdn){if(sdn!=T_I.susdivname){T_I.take_chosen(T_I.susdivname,0);T_I.susdivname=sdn;T_I.susdivo=gob(sdn)}}
this.populate_sus=function(sdn,thisq,f){if(f)chosen_sus[sdn]=sus[thisq].length
var ts=new Date(),su=sus[thisq],slist='<table>',ch,ncn='',n=sdn.replace('susdiv',''),BO,td2l,td2r=''
if(su.length!==0)
for(var i in su){if(i==='indexOf')break
if(2<su[i].length){if((0<su[i][5])&&(su[i][5]<T.getTime())){su[i][4]=su[i][6]
su[i][5]=0}
ts.setTime(T.getTime()+(su[i][4])*60000)
BO=format_time(ts,0).replace(/<\/?span.*?>/g,'')
if(typeof su[i][7]=='string')su[i][7]=su[i][7].split(',')
for(j=0;j<4;j++){su[i][j]=su[i][j].replace(/\$6/g,su[i][7][2]).replace(/\$5/g,su[i][7][1]).replace(/\$4/g,su[i][7][0]).replace(/\$2/g,su[i][2]).replace(/\$1/g,su[i][1]).replace(/\$/g,su[i][0]);if(su[i][j]=='')su[i][j]=su[i][0]}
su[i][2]=su[i][2].replace(/ /g,'_')
ch='';if(i==chosen_sus[sdn])ch='" class="chosen'
td2l='<td class="t"><a href="'+susdest+su[i][2]+susdestquery+'" onclick="return T_I.susgo(\''+jsesc(su[i][0])+'\',\''+jsesc(su[i][1])+'\',\''+jsesc(su[i][2])+'\',\''+jsesc(su[i][3])+'\')" onmouseover="T_I.choose_sus(\''+sdn+'\','+i+')"><span>'+BO+'</span></a></td>'
slist+='<tr valign="top" id="'+sdn+'s'+i+ch+'">'+td2r+'<td><a href="'+susdest+su[i][2]+susdestquery+'" onclick="return T_I.susgo(\''+jsesc(su[i][0])+'\',\''+jsesc(su[i][1])+'\',\''+jsesc(su[i][2])+'\',\''+jsesc(su[i][3])+'\')" onmouseover="T_I.choose_sus(\''+sdn+'\','+i+')"><span>'+su[i][1]+'</span></a></td>'+td2l+'</tr>'}}else{td2l='<td class="t">&nbsp;</td>'
if(ltr!=1){td2r=td2l;td2l=''}
slist+='<tr>'+td2r+'<td><a href="./"><span>'+p_no_match+'</span></a></td>'+td2l+'</tr>'
ncn='error '}
gob(sdn).innerHTML=slist+'</table>'
gob('q'+n).className=gob('q'+n).className.replace('error','').replace('txtin ','txtin '+ncn)
current_q[sdn]=thisq
if(0<su.length&&sdn!='susdiv'&&T_I.susdivname!=sdn){complocurls[n]=[su[0][3],su[0][2]]
gob('q'+n).value=su[0][3].replace('&nbsp;',' ')
gob(sdn).innerHTML=''
current_q[sdn]=''
return ''}}
this.cycle_sus=function(sdn,n){if((current_q[sdn]!='')&&(typeof sus[current_q[sdn]]!=U)){var new_chosen=chosen_sus[sdn]+n
if(new_chosen<0)new_chosen=sus[current_q[sdn]].length-1
if(new_chosen>=sus[current_q[sdn]].length)new_chosen=0
this.choose_sus(sdn,new_chosen)}}
this.choose_sus=function(sdn,i){if(typeof sus[current_q[sdn]]!=U){if(i!==chosen_sus[sdn]&&chosen_sus[sdn]<sus[current_q[sdn]].length)gob(sdn+'s'+chosen_sus[sdn]).className=''
var o=gob(sdn+'s'+i)
if(o!=N){o.className='chosen';chosen_sus[sdn]=i}}}
this.update_favs_app=function(T,BS){if(0==locs['favs'].length)return ''
var html='',BY,BZ='favs'
for(BY in locs[BZ]){var CA=new Date(),CB=locs[BZ][BY][1],BO,CC,CD,brbeforetime=''
CA.setTime(T.getTime()+get_zone_offset(CB,T)*60000)
BO=brbeforetime+'<span class="wd">'+daysh[CA.getUTCDay()]+'</span> '
+'<span class="time">'+format_time(CA,0)+'</span>'
if(locs[BZ][BY][9])CD=locs[BZ][BY][9]
else CD=locs[BZ][BY][2]
if(BS){CC=''
if(BY==Tstate.chosen_loc)CC='" class="chosen'
html+='<h2 id="favbox-'+BY+'" class="fav"'
html+='><a href="'+locs[BZ][BY][4].replace('$1',locs[BZ][BY][2])
html+=CC+'" id="time-'+BY+'"><span class="locname">'+CD+'</span><span class="longlocname">'+locs[BZ][BY][3].replace(CD+', ','')+' </span>'+BO+'</a>'
html+='</h2>'}else{CC=gob('time-'+BY)
if(CC&&CC.innerHTML!=CD+'<br>'+BO)CC.innerHTML='<span class="locname">'+CD+'</span><span class="longlocname">'+locs[BZ][BY][3].replace(CD+', ','')+' </span>'+BO}}
CC=gob('favs')
if(BS&&CC){CC.innerHTML=html}
if(locs['favs'].length<2){gob('favs').className='tbx homealone'}}
this.reposition_favboxes=function(CE){var BY,CF,CG,x=0,y=0
for(BY in locs[CE]){CF=gob('favbox-'+BY)
CG=CF.offsetWidth
if(BY!=0&&ww*.9<x+CG){y+=CF.offsetHeight
x=0}
CF.style.top=y+'px'
if(ltr)CF.style.right=x+'px'
else CF.style.right=x+'px'
x+=CF.offsetWidth}}
function jsesc(s){return s.replace("'","\\\'").replace('"','\\\'')}
sus=[]
chosen_sus=[]
prevsustime=[]
current_q=[]
prevq=[]
this.susgo=function(nm,p,u,d){
if(T_I.susdivname=='susdiv'||T_I.susdivname=='susdivB')location=susdest+u+susdestquery
else{losefocus(T_I.susdivname)
var n=T_I.susdivname.replace('susdiv','')
gob('q'+n).value=d.replace('&nbsp;',' ')
complocurls[n]=[d,u]
T_I.susdivo.innerHTML=''
n++
if(gob('q'+n)!=N)setfocus('q'+n)
}
return F}
this.take_chosen=function(sd,k){
if(sd!='susdiv'&&sd!='susdivB'){var sn=chosen_sus[sd],q=current_q[sd],W=sus[q]
if(k==13&&q==''){submission=E
return E}
qv=gob('q'+sd.replace('susdiv','')).value
if(qv.length<4)qv=qv.toLowerCase()
if(q!=qv)return F
if(typeof W!=U){if(W.length==0)return F
if(W.length==sn)sn=0
W=W[sn]
T_I.susgo(W[0],W[1],W[2],W[3])}
if(k==13){submission=F
var n=sd.replace('susdiv','')
n++
if(gob('q'+n)!=N){gob('q'+n).focus()}}
else submission=E}
return F}
this.submit=function(q){var u='',sdn=T_I.susdivname,i=chosen_sus[sdn]
if(q!=current_q[sdn]){console.log('no s')
return E}
if(current_q[sdn]!=''){if(i>=sus[current_q[sdn]].length)i=0
if(sus[current_q[sdn]].length!==0)u=sus[current_q[sdn]][i][2]
else if(q.length!=0)return E}
T_I.susgo('','',u,'')
return F}
this.reconf=function(){this.recook()
var cl=gob('confl')
if(cl)cl.href=cl.href.replace(/c=.+&/,'c='+confs+'&')
BC=0
clocks[0].prevDate=0
clocks[0].time_format=conf['t']
clocks[0].date_format=conf['d']
if(-1!==conf['t'].indexOf('M'))updint=33
else if(-1!==conf['t'].indexOf('T'))updint=100
else updint=1000
this.tick('',0)}}
function setsizes(f){ww=D.body.offsetWidth?D.body.offsetWidth:window.innerWidth?window.innerWidth:1
if(D.body.offsetWidth&&window.innerWidth){ww=Math.min(D.body.offsetWidth,window.innerWidth)}
if(wh==1||bfc)wh=window.innerHeight?window.innerHeight:D.documentElement.clientHeight?D.documentElement.clientHeight:D.body.clientHeight
previous_aspect=aspect
aspect='portrait'
if(wh<ww)aspect='landscape'
bod.className=bod.className.replace(/ portrait|landscape/,'')+' '+aspect
place_badges()
set_clock_display(clocks[0].timeDiv,clocks[0].ticktime,clocks[0].ms)
}

function CL(CS,CT,CU,CV,CW){var CX=Math.round(CS*CT*.99)
if(CU<CX)CX=CU
CX=Math.max(Math.floor(CX*CW),CV)
CM=Math.floor((CT-CX/CS)/2)
return [CX,CM]}
function setmsgH(){var o=gob('time_section')
cvT=0
if(dmode||!noctp)cvT=Math.max(0,(wh-o.offsetHeight)/2-gob('top').offsetHeight-30)
o.style.marginTop=cvT+'px'
if(Tstate.current_page=='Unix_time_now')gob('time_section').style.marginBottom=(wh-gob('clock0_bg').offsetHeight)/2+'px'}
function set_clock_aspect(){CZ=gob('clock0_bg')
clock_aspect=CZ.offsetHeight/CZ.offsetWidth}
function beginning_of_time(){clocks=[]
arrayname='main'
clocks[0]=new clock(arrayname,Tstate.chosen_loc,'clock','dd','daydiv','sun','pL','facts0',conf['t'],conf['d'],0)
scrollTo(0,0)
set_clock_aspect()
setsizes(2)
T_I=new TimeIs('clock','dd','daydiv','pL','lC')
T_I.initClock()}
function clock(CE,DA,DB,AV,AW,DC,AX,DD,DE,DF,DG){this.timeDiv=gob(DB)
this.dateDiv=gob(AV)
this.dayDiv=gob(AW)
this.sunDiv=gob(DC)
this.prevDate=0
this.t=0
this.time_format=DE
this.date_format=DF
this.locName=''
this.id=DG
this.setLoc=function(CE,DH,BT){var DI=locs[CE].length,DJ,n=0
DH=(DH+DI+n)%(DI+n)
DJ=locs[CE][DH]
if(!BT&&CE==AZ.arrayname&&DH==AZ.index)return F
AZ.locName=DJ[2]
AZ.prettyPath=DJ[3].replace(/\$1/g,DJ[2])
if(DJ[9])AZ.locName=AZ.prettyPath=DJ[9]
AZ.zone_code=DJ[1]
AZ.arrayname=CE
AZ.index=DH
update_big_clock(BQ,AZ,1)
var DK='w90 tr clockdate',DL=''
AZ.dayDiv.className=DK
if(typeof pidaypage!==U)DK='w90'
AZ.dateDiv.className=DK
return E}
var AZ=this
this.setLoc(CE,DA)}
function tl_a(){if(auSt==0){try{if((!!D.createElement('audio').canPlayType)&&(-1===navigator.userAgent.indexOf('MSIE'))){if(t_au1m===0){t_au1s=new Audio()
var audiotype='.mp3'
if(t_au1s.canPlayType('audio/ogg')!=='')audiotype='.ogg'
var lp=1;if(-1===navigator.userAgent.indexOf('WebKit')){audiotype='_'+audiotype;lp=0}}
for(var i in auf)eval('if(t_au1m===0){t_au'+auf[i]+'=new Audio();t_au'+auf[i]+'.src="/audio/'+auf[i]+'"+audiotype;if(lp)t_au'+auf[i]+'.loop=E}t_au'+auf[i]+'.volume=0;t_au'+auf[i]+'.play()')
auSt=1
gob('aub').className='chosen'}else alert(p_no_au)}catch(err){alert(p_no_au)}}else{for(var i in auf){eval('t_au'+auf[i]+'.pause()')};auSt=0
gob('aub').className=''}
var lp='p0 ';if(auSt)lp=''}
function get_sb(k){var fun={"f":(function(){var d=document,s='script',id='facebook-jssdk',js,fjs=d.getElementsByTagName(s)[0]
if(d.getElementById(id))return
js=d.createElement(s);js.id=id
js.src='//connect.facebook.net/'+lang+'/all.js#xfbml=1'
fjs.parentNode.insertBefore(js,fjs)}),"t":(function(){var DM='right'
if(ltr!=1)DM='left'
gob('socb_t').innerHTML='<div style="margin-'+DM+':10px"><a href="https://twitter.com/share" class="twitter-share-button" data-text="'+tweet+'" data-lang="'+glang+'" data-via="Time_is" data-size="large" data-count="none">Tweet</a></div><div style="clear:none"><a href="https://twitter.com/Time_is" class="twitter-follow-button" data-size="large" data-show-count="false" data-show-screen-name="true" data-lang="'+glang+'">Follow @Time_is</a></div>'
var d=document,s="script",id="twitter-wjs",js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}})}
fun[k]()}
function sb_o(k,x){if(x){if(s_on!=k){var f=0
if(typeof soc_a[k]==U)f=1
if(s_on)sb_o(s_on,0)
gob('btn_'+k).style.backgroundPosition=rg[k]+'px 0'
gob('socbuttons').className=''
gob('socb_'+k).className=gob('social_div').className=''
var h=44;if(k=='f')h=72;if(k=='m'){h=380;gob('sender_name').focus()}gob('social_div').style.height=h+'px'
s_on=k
if(f)get_sb(k)}}else{if(soc_a[k]==1){soc_a[k]=0
if(k=='m')gob('sender_name').blur()
gob('social_div').className=gob('socb_'+k).className='hide'
gob('btn_'+k).style.backgroundPosition=rg[k]+'px 32px'
gob('socbuttons').className='off'
s_on=F}}
soc_a[k]=x
return F}
function t_sb(k){if(s_on===k)sb_o(k,0)
else sb_o(k,1)
return F}
function hv_sb(k,y){if(s_on!==k)Zpos(gob('btn_'+k),rg[k],y)}
function Zpos(e,x,y){e.style.backgroundPosition=x+'px '+y+'px'}
function t_dark(){var c=bod.className
if(-1!=c.indexOf(' d ')){bod.className=c.replace(' d ',' l ')
if(conf['c']==1)conf['c']=0}else{bod.className=c.replace(' l ',' d ')
if(conf['c']==0)conf['c']=1}
T_I.recook()}
function t_s(){var X=conf['t'][1]
if('i'==X)X=''
if(-1==conf['t'].indexOf('s'))conf['t']=conf['t'].replace('i','i'+X+'s')
else if(-1!==conf['t'].indexOf('M'))conf['t']=conf['t'].replace(X+'s.M','')
else if(-1==conf['t'].indexOf('T'))conf['t']=conf['t'].replace('s','s.T')
else conf['t']=conf['t'].replace('s.T','s.M')
T_I.reconf()
set_clock_aspect()
setsizes(1)
clearTimeout(T_I.timeout)
T_I.tick('',1)}
function clockclick(e){
if(conf['B']==1&&e.clientX>ww/2)t_s()
else togglesimple(2)
}
function chk(o){return o.checked?1:0}
function get_zone_offset(DO,T){if(typeof zones[DO][2]=='string')DO=zones[DO][2]
if(zones[DO].length==3){zones[DO][3]=zones[DO][2][0]
zones[DO][4]=1}
while(zones[DO][4]<zones[DO][2].length&&zones[DO][2][zones[DO][4]]<=T.getTime()/1000){zones[DO][3]=zones[DO][2][zones[DO][4]+1]
zones[DO][4]+=2
sus=[]
chosen_sus=[]
prevsustime=[]
current_q=[]}
return zones[DO][3]}
function update_big_clock(BQ,DP,DQ){ww=D.body.offsetWidth?D.body.offsetWidth:window.innerWidth?window.innerWidth:1
if(D.body.offsetWidth&&window.innerWidth){ww=Math.min(D.body.offsetWidth,window.innerWidth)}
var BN=new Date(T.getTime()),
DR=get_zone_offset(DP.zone_code,BN),
DS=DR*60000
BV=DP.time_format
BN.setTime(BN.getTime()+DS)
DP.t=BN
var h=BN.getUTCHours(),H=h,DT='am',secs=BN.getUTCSeconds(),ms=l0(BN.getUTCMilliseconds());ms=ms>99?ms+'':"0"+ms
DP.hours=h
DP.minutes=BN.getUTCMinutes()
if(11<h){DT='pm';h-=12}
if(h==0)h=12
var DU=build_date(BN,DP.date_format)
if(DP.id==0){tw0=gob('timewindow0')}
if(force24)BV=BV.replace('h','H').replace('A','')
BV=BV.replace('g',h).replace('h',l0(h)).replace('H',l0(H)).replace('i',l0(BN.getUTCMinutes())).replace('s',l0(secs+Ltmpcorr)).replace('t','X').replace('M','THT').replace('A',DT).replace('@',Math.floor(T.getTime()/1000))
if(Tstate.current_page=='Unix_time_now'){
BV=spanwrap_digitz(BV.replace(/<\/?span.*?>/g,'').replace(/\.T(HT)?/,''))
if(!dmode)BV+=' '+DU.replace(/<\/?span.*?>/g,'')
gob('smalltime').innerHTML=BV
var d=0
if(-1!=DP.time_format.indexOf('M'))d=3
if(-1!=DP.time_format.indexOf('T'))d=1
BV=(T.getTime()/1000).toFixed(d)+''
DU=''}
CI=0
if(/MSIE/.test(navigator.userAgent))CI=1
DP.ticktime=BV
DP.ms=ms
set_clock_display(DP.timeDiv,BV,ms)
today_in_chosen_tz=new Date(Date.UTC(BN.getUTCFullYear(),BN.getUTCMonth(),BN.getUTCDate()))
if(DQ||today_in_chosen_tz!=DP.prevDate){
todayY=today_in_chosen_tz.getUTCFullYear()
todayM=l0(today_in_chosen_tz.getUTCMonth()+1)
todayD=l0(today_in_chosen_tz.getUTCDate())
today_iso=todayY+'-'+l0(todayM)+'-'+l0(todayD)
mmdd=todayM+''+todayD
DV=mmdd
today_in_home_tz_iso=build_date(BN,'%Y-%m-%d')
if(caltoday){
var _td=gob(caltoday)
if(_td)_td.className=_td.className.replace(/ *today/,'')
caltoday='d_'+build_date(BN,'%Y_%n_%j_%w')
_td=gob(caltoday)
if(_td)_td.className=_td.className+' today'
}
if(DP.prevDate!=0){if(DP.sunDiv&&typeof ss[DV]!=U)DP.sunDiv.innerHTML=ss[DV]
else gob('locw').innerHTML=''}
DP.dateDiv.innerHTML=DU
displayed_date=DU
if(typeof spdays[DV]!=U&&spdays[DV]!=''){DP.dayDiv.innerHTML=spdays[DV]
DP.dayDiv.className='w90 tr noprint'}
else DP.dayDiv.className='hidden'
DP.prevDate=DV}}
function spanwrap_digitz(s,c){if(c===undefined)c=' class="mon"'
var i=0,r=''
s+=r
for(;i<s.length;i++)r+=s[i].replace(/(\d)/,'<span'+c+'>$1</span>')
return r}
function force_monospace(s,a,m){
var l=s.length,i=0,r='',c,t=0,w,ml,fs,cl
for(;i<l;i++){
c=s.substr(i,1)
fs=''
cl=''
if(-1<':. '.indexOf(c)){w=.6;cl=' class="sep"'}
else if(-1!=='APMapmTH'.indexOf(c)){
fs=';ampmfs'
w=.8
}
else w=1
r+='<span'+cl+' style="width:w'+w+'px;margin-left:-mpx'+fs+'">'+c+'</span>'
t+=w
}
w=Math.min(Math.floor(a/(t-(l-1)/4)),m)
ml=-Math.floor(w/4)
return {w:w,margin:ml,totalwidth:Math.ceil(t*w+(l-1)*ml),html:r.replace(/w1/g,w).replace(/w0\.6/g,Math.floor(w*.6)).replace(/w0\.8/g,Math.floor(w*.8)).replace(/-m/g,ml)}
}
displayed_time=''
displayed_date=''
function set_clock_display(o,s,ms){
displayed_time=s.replace('T',ms[0]).replace('H',ms[1]).replace('T',ms[2])
var fontwidthfactor={colfax:1.1,colfax_bold:1.15,mt:1.05,monts:.95,mr:1,t:1.4,h:1.3,a:1,a2:1,test:1.3},w=ww,r,fs,fwf=fontwidthfactor[conf['f']]
if(typeof fwf===U)fwf=1
w=ww*.9*conf['z']
r=force_monospace(s,w,999)
fs=Math.floor(r.w*1.1*fwf)
r.html=r.html.replace(/;ampmfs/g,';font-size:'+Math.floor(fs/1.6)+'px').replace('T',ms[0]).replace('H',ms[1]).replace('T',ms[2])
o.style.fontSize=fs+'px'
o.style.lineHeight=Math.floor(fs*.7)+'px'
o.style.marginLeft=((ww-r.totalwidth)/2-r.margin)+'px'
o.innerHTML=r.html
}
function change_clock_size(){
conf['z']=conf['z']*1+.2
if(1<conf['z'])conf['z']=.4
set_clock_display(clocks[0].timeDiv,clocks[0].ticktime,clocks[0].ms)
setmsgH()
T_I.recook()
}
function goto_element(e){var o=gob(e);if(o)if(o.scrollIntoView)o.scrollIntoView({behavior:'smooth'});else location.hash=e}
function nice_approx_time(n,t,g,DW){d=Math.round(n-t)
var a=Math.abs(d),h='',i=0,n
if(a>86400)
a=Math.abs(Math.floor(n/86400)-Math.floor(t/86400))
else if(a>59){h=Math.floor(a/3600)
a=Math.round(a%3600/60)
if(a==60){a=0;h++}
if(h>0){if(1==h)h=h+p_h
else h=sppl(h,"%n"+p_hs)
if(a==0)a=''
else h+=p_and}else h=''
i=2}else
i=3
if(-DW<d&&d<1)return p_now
if(1==a)a=a+units_sing[3-i]
else if(a!='')a=sppl(a,"%n"+units[3-i])
if(d<0)return p_ago.replace('%n',h+a)
return p_in_n.replace('%n',h+a)}
function make_nice_zone_diff_sentence(DX,DY,DZ,EA){var EB=DY-EA,EC,ED,EE
if(EB==0)
EC=p_zone_diff_same
else{EF=Math.floor(Math.abs(EB/60))
if(EF==1)ED=1+p_h
else ED=sppl(EF,'%n'+p_hs)
EE=Math.abs(EB%60)
if(EE==0)EE=''
else{if(EF==0){ED=''
EE=sppl(EE,'%n'+p_ms)+' '}else EE=p_and+sppl(EE,'%n'+p_ms)+' '}
if(EB<0)EC=p_a_behind_b
else EC=p_a_ahead_of_b}
return EC.replace('%a',DX).replace('%b',DZ).replace('%t',ED+EE)}
var last_menu_btn_click=0
function toggle_menu(x){
var t=new Date().getTime(),shortbody=F,c=bod.className,h=0,mp=gob('menupositioner'),mwr=gob('mainwrapper')
if(t-last_menu_btn_click<400)return F
last_menu_btn_click=t
scrollTo(0,0)
if(c.indexOf('menu_open')!=-1){
if(x===1)return F
mp.style.transform='translateY(0px)'
c=c.replace(' menu_open',' menu_closing')
hideMTO=setTimeout(close_menu,350)
}
else{
if(!x)return F
c=c.replace(' shortbody','').replace(' tallbody','')
var html=document.documentElement,pageheight=Math.max(bod.scrollHeight,bod.offsetHeight,html.clientHeight,html.scrollHeight,html.offsetHeight)
if(Tstate.current_page!=='just_time'&&pageheight-mp.offsetHeight<wh){c+=' shortbody';shortbody=E}
else c+=' tallbody'
c=c+' menu_open'
h=mp.offsetHeight
// if(h<wh)h=wh
// mp.style.height=h+'px'
if(shortbody)mp.style.transform='translateY(-'+(mwr.offsetHeight+h)+'px)'
}
bod.className=c
mwr.style.marginTop=h+'px'
return F
}
function close_menu(){bod.className=bod.className.replace(' menu_closing','');gob('menupositioner').style.height='auto'}
function addclass(i,c){gob(i).className+=' '+c}
function removeclass(i,c){gob(i).className=gob(i).className.replace(' '+c,'')}
var auf=['1s','59s','1m'],t_au1s=0,t_au59s=0,t_au1m=0,auSt=0,CI=0,confs='',rg={f:224,t:192},cvT=0,zm=['z','z'],BQ=21,prevT=0,nextSyncT=0,susdestquery='',caltoday=F,today_in_home_tz_iso=F,aspect='landscape',Ltmpcorr=0,Loffset=0,prevTS=0,leapTS=1483228800,s_on=0,soc_a=[],alarm_time=0,ww=1,wh=1,clock_aspect=0.2,T=new Date(),favHeight=34
EG=[]
function update_colors_depending_on_sun(BT){conf['c']=conf['c']*1
var arr,k
if(conf['c']==2){if(locs['favs'].length<1)return
arr='favs'
i=conf['h']}else if(conf['c']==3){if(clocks.length<1)return
if(locs[clocks[0].arrayname].length<1)return
arr=clocks[0].arrayname
i=clocks[0].index}else return
var BN=new Date(T.getTime()+get_zone_offset(locs[arr][i][1],T)*60000),DV=l0(BN.getUTCMonth()+1)+''+l0(BN.getUTCDate())
if(typeof EG[arr+i]===U)EG[arr+i]=[]
if(BT||typeof EG[arr+i].prevDate==U||DV!=EG[arr+i].prevDate){EG[arr+i].suntimes=update_suntimes(locs[arr][i],T,0)
EG[arr+i].prevDate=DV}
if(!EG[arr+i].suntimes)return
var CC=0
if(EG[arr+i].suntimes.sunrise<T&&T<EG[arr+i].suntimes.sunset)CC=1
if(Tstate.current_page=='main'&&1<conf['c']*1&&(BT||typeof EG[arr+i].sunclass==U||EG[arr+i].sunclass!==CC)){toggle('bdy',' d ',' l ',CC)
EG[arr+i].sunclass=CC}}
function place_badges(){var c='',o=gob('follow_app');if(o){if(100<o.offsetTop)c='below';o.className=c}}
function setcol(v){conf['c']=v;if(v==0)bod.className=bod.className.replace(' d ',' l ');else bod.className=bod.className.replace(' l ',' d ');T_I.reconf()}
function t_FS(){if(D.fullscreenElement){if(D.exitFullscreen)D.exitFullscreen()
else if(D.mozCancelFullScreen)D.mozCancelFullScreen()
else if(D.webkitExitFullscreen)D.webkitExitFullscreen()
else if(D.msExitFullscreen)D.msExitFullscreen()}else{var E=D.documentElement
if(E.requestFullscreen)E.requestFullscreen()
else if(E.mozRequestFullScreen)E.mozRequestFullScreen()
else if(E.webkitRequestFullscreen)E.webkitRequestFullscreen()
else if(E.msRequestFullscreen)E.msRequestFullscreen()}}
D.onfullscreenchange=function(e){if(D.fullscreenElement)togglesimple(0);else togglesimple(1)}
popmsgTO=F
popmsgexitTO=F
function notify(c,m,no_retrigger){
gob('popmsgtext').innerHTML=m
var o=gob('popmsgbg')
o.className=''
if(!no_retrigger)void o.offsetWidth
o.className='enter'
gob('popmsg').className=c
gob('popmsg').style.display='block'
clearTimeout(popmsgTO)
clearTimeout(popmsgexitTO)
popmsgTO=setTimeout('remove_popmsg()',2000)
}
function remove_popmsg(){
gob('popmsgbg').className='exit'
clearTimeout(popmsgTO)
popmsgTO=F
clearTimeout(popmsgexitTO)
popmsgexitTO=setTimeout('remove_popmsg2()',350)
}
function remove_popmsg2(){
gob('popmsg').style.display='none'
clearTimeout(popmsgexitTO)
popmsgexitTO=F
}
function animate_if_onscreen(){
for(var i in animatable){
var o=gob(animatable[i])
if(o){r=o.getBoundingClientRect(),cn=o.className.replace(/ *onscreen/,'').replace(/ *offscreen/,''),m=Math.min(300,wh*.7)
if((0<r.top&&r.top<wh-m)||(m<r.bottom&&r.bottom<wh)||(r.top<0&&wh<r.bottom))cn+=' onscreen'
else cn+=' offscreen'
o.className=cn
}}}
animatable=[]
animInterval=F
function register_anim(id){
animatable.push(id)
animate_if_onscreen()
if(!animInterval)animInterval=setInterval('animate_if_onscreen()',400)
}
var open_faq=false
function toggle_faq(p){
var o=p.parentElement,h='0',cln='closed',dw=o.getElementsByClassName('faq_answer_wrapper')[0]
if(o.className.indexOf('open')===-1){
h=dw.getElementsByClassName('faq_answer')[0].offsetHeight
cln='open'
if(open_faq!==F&&open_faq!==p)toggle_faq(open_faq)
open_faq=p
}else open_faq=F
dw.style.height=h+'px'
o.className='faqitem '+cln
}
function makediv(i,c,h){var d=gob(i)
if(!d){d=D.createElement('div');d.id=i;bdy.prepend(d)}
if(c!==undefined)d.className=c
if(h!==undefined)d.innerHTML=h
return d
}
function setpoppos(a,b,dX,dY,minW){
a=gob(a)
b=gob(b)
if(!a||!b)return;
var r=a.getBoundingClientRect(),x=r.left+window.pageXOffset,y=r.top,top=bottom=left=right='auto',oH=a.offsetHeight,oW=a.offsetWidth,wh=D.documentElement.clientHeight
if(y<wh/2)top=y+oH*(1+dY)+window.pageYOffset+'px'
else bottom=wh-y+oH*dY-window.pageYOffset+'px'
if(ww<minW)left=0
else if(x<ww/2)left=x-oW*dX+'px'
else right=ww-x-oW*(1+dX)+'px'
b.style.top=top
b.style.bottom=bottom
b.style.left=left
b.style.right=right
}
function show_datechooser(q){
var x='datechooser',o=makediv(x,x+' popc show')
setpoppos(q,x,0,0,680)
render_cal(x,0)
}
function close_datechooser(){var o=gob('datechooser');if(o){o.className='hide'}}
function set_inpv(o,i){var c=i.split('_');if(3<c.length)o.value=sh_d(new Date(Date.UTC(c[1],c[2]-1,c[3])))}
function goto_this_year(){if(!gob('calendar_body'))return;calY=todayY;calM=1;render_cal('calendar_body',0)}
var adBlockEnabled=F,AdP=AdP||''
function detectAdBlock(cb){
var axR=N
if(window.XMLHttpRequest)axR=new XMLHttpRequest()
else if(window.ActiveXObject)axR=new ActiveXObject('Microsoft.XMLHTTP')
if(axR!=N){axR.onreadystatechange=function(){if(this.readyState==4){if(this.status==200)Tell(F)
else Tell(E)}}
axR.open('head','https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',E)
axR.send(N)}else Tell(E)}
function Tell(x){adBlockEnabled=x;x=x?'BL':'OK';var i=document.createElement('img');i.src='/img/nod.png?'+x+'_'+AdP+T.getTime();i.style='width:1px;height:1px;position:absolute';bdy.appendChild(i)}
