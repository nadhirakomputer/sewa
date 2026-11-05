<?php
require __DIR__ . '/db.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
header('Cache-Control: no-store');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Manajemen Sewa Barang</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;800&display=swap">
<style>
:root{--bg:#f2f4f6;--sf:#fff;--ink:#17232e;--mute:#5d6b78;--line:#d8dee4;--ac:#0b6b7c;--aci:#fff;--bad:#b3261e;--badbg:#fbe9e7;--ok:#1f7a4d;--okbg:#e4f3ea;--warn:#8a5000;--warnbg:#fdf0dc;--info:#0b6b7c;--infobg:#e1f1f4;
box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
@media(prefers-color-scheme:dark){:root:not([data-theme="light"]){--bg:#0f171e;--sf:#17222c;--ink:#e6edf3;--mute:#93a3b1;--line:#2a3845;--ac:#4fc0d3;--aci:#08222a;--bad:#ff8a80;--badbg:#3a1a18;--ok:#6fd39c;--okbg:#153324;--warn:#f0b35a;--warnbg:#3a2a10;--info:#4fc0d3;--infobg:#123038;color-scheme:dark}}
:root[data-theme="dark"]{--bg:#0f171e;--sf:#17222c;--ink:#e6edf3;--mute:#93a3b1;--line:#2a3845;--ac:#4fc0d3;--aci:#08222a;--bad:#ff8a80;--badbg:#3a1a18;--ok:#6fd39c;--okbg:#153324;--warn:#f0b35a;--warnbg:#3a2a10;--info:#4fc0d3;--infobg:#123038;color-scheme:dark}
html{scroll-padding-top:env(safe-area-inset-top,0px)}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 "Plus Jakarta Sans",system-ui,-apple-system,"Segoe UI",sans-serif}
.wrap{max-width:1120px;margin:0 auto;padding:20px 16px 48px}
h1{font-size:26px;font-weight:800;margin:0 0 2px;letter-spacing:-.02em}
h2{font-size:17px;font-weight:600;margin:28px 0 10px}
.sub{color:var(--mute);margin:0 0 18px}
nav{display:flex;gap:4px;border-bottom:1px solid var(--line);margin-bottom:20px;overflow-x:auto}
nav button{background:none;border:0;border-bottom:3px solid transparent;color:var(--mute);font:inherit;font-weight:600;padding:10px 14px;cursor:pointer;white-space:nowrap}
nav button.on{color:var(--ink);border-bottom-color:var(--ac)}
button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible{outline:2px solid var(--ac);outline-offset:2px}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px}
.st{background:var(--sf);border:1px solid var(--line);border-radius:8px;padding:12px 14px}
.st b{display:block;font-size:22px;font-weight:800}.st span{color:var(--mute);font-size:13px}
.st.bad b{color:var(--bad)}
.bar{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:12px}.bar .sp{flex:1}
.btn{background:var(--ac);color:var(--aci);border:0;border-radius:6px;padding:8px 14px;font:inherit;font-weight:600;cursor:pointer}
.btn.g{background:none;color:var(--ink);border:1px solid var(--line)}.btn.d{background:var(--bad);color:#fff}.btn.s{padding:4px 10px;font-size:13px}
.tw{overflow-x:auto;background:var(--sf);border:1px solid var(--line);border-radius:8px}
table{border-collapse:collapse;width:100%;min-width:640px}
th,td{text-align:left;padding:9px 12px;border-bottom:1px solid var(--line);vertical-align:top}
th{font-size:13px;color:var(--mute);font-weight:600;background:var(--bg)}tr:last-child td{border-bottom:0}
td.n,th.n{text-align:right;white-space:nowrap}.act{white-space:nowrap;display:flex;gap:6px}
.bd{display:inline-block;border-radius:99px;padding:1px 10px;font-size:12.5px;font-weight:600;white-space:nowrap}
.bd.bad{background:var(--badbg);color:var(--bad)}.bd.ok{background:var(--okbg);color:var(--ok)}.bd.warn{background:var(--warnbg);color:var(--warn)}.bd.info{background:var(--infobg);color:var(--info)}
.mute{color:var(--mute)}.emp{padding:22px;text-align:center;color:var(--mute)}
select,input,textarea{font:inherit;color:var(--ink);background:var(--sf);border:1px solid var(--line);border-radius:6px;padding:7px 9px;width:100%}
.bar select{width:auto}
label{display:block;font-size:13px;font-weight:600;margin:12px 0 4px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
dialog{border:1px solid var(--line);border-radius:10px;background:var(--sf);color:var(--ink);padding:20px;width:min(480px,calc(100vw - 24px))}
dialog::backdrop{background:rgba(0,0,0,.45)}
dialog h3{margin:0 0 4px;font-size:18px}
.ft{display:flex;gap:8px;justify-content:flex-end;margin-top:18px}
.pv{background:var(--bg);border-radius:8px;padding:10px 12px;margin-top:14px;font-size:14px}.pv b{font-weight:800}
.err{color:var(--bad);font-size:13px;margin-top:8px;min-height:1em}
.top{display:flex;justify-content:space-between;margin-bottom:14px}.top a{color:var(--ac);font-weight:600;text-decoration:none}
.th{width:40px;height:40px;border-radius:6px;object-fit:cover;background:var(--line);display:inline-flex;align-items:center;justify-content:center;font-weight:700;color:var(--mute);cursor:zoom-in;flex:none}
.th.ph{cursor:default}.th.big{width:72px;height:72px;cursor:default}.pp{display:flex;gap:10px;align-items:center}.fp{display:flex;gap:12px;align-items:center}.fp input[type=file]{padding:4px}.fp .btn{margin-top:6px}[hidden]{display:none!important}
</style>
</head>
<body>
<div class="wrap">
<div class="top"><a href="index.php">Halaman depan</a><a href="logout.php">Keluar</a></div>
<h1>Manajemen Sewa Barang</h1>
<p class="sub">Catat barang, pelanggan, tanggal sewa, tanggal kembali, keterlambatan, dan denda.</p>
<nav id="nav"></nav>
<div id="view"></div>
</div>
<dialog id="dlg"><h3 id="dt"></h3><div id="db"></div><div class="err" id="er"></div><div class="ft"><button class="btn g" id="cx">Batal</button><button class="btn" id="ok">Simpan</button></div></dialog>
<script>
const $=s=>document.querySelector(s);
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const rp=n=>'Rp'+Number(n||0).toLocaleString('id-ID');
const pd=s=>{const[y,m,d]=s.split('-').map(Number);return Date.UTC(y,m-1,d)};
const diff=(a,b)=>Math.round((pd(b)-pd(a))/864e5);
const add=(s,n)=>new Date(pd(s)+n*864e5).toISOString().slice(0,10);
const today=()=>{const d=new Date();return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0')};
const fd=s=>s?new Date(pd(s)).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric',timeZone:'UTC'}):'—';
const v=id=>$('#'+id).value.trim();
let S={items:[],customers:[],rentals:[]};
async function api(a,d){const r=await fetch('api.php?action='+a,{method:d?'POST':'GET',headers:{'Content-Type':'application/json','X-Requested-With':'fetch'},body:d?JSON.stringify(d):undefined});if(r.status==401){location.href='login.php';throw new Error('Sesi habis')}let j={};try{j=await r.json()}catch(e){}if(!r.ok||j.error)throw new Error(j.error||'Server error ('+r.status+')');return j}
const load=async()=>{S=await api('all')};
const item=id=>S.items.find(i=>i.id==id);
const cust=id=>S.customers.find(c=>c.id==id);
function calc(r){const it=r;const end=r.returned||today();
const days=Math.max(1,diff(r.start,r.due)),late=Math.max(0,diff(r.due,end));
const st=r.returned?(late?['Selesai, telat','warn']:['Selesai','ok']):late?['Terlambat','bad']:['Berjalan','info'];
return{days,late,rent:days*it.rate,fine:late*it.fine,total:days*it.rate+late*it.fine,st}}
const active=iid=>S.rentals.find(r=>r.itemId==iid&&!r.returned);
let tab='ringkasan',flt='semua';
const TABS=[['ringkasan','Ringkasan'],['sewa','Penyewaan'],['barang','Barang'],['pelanggan','Pelanggan']];
function go(t){tab=t;render()}
function render(){
$('#nav').innerHTML=TABS.map(([k,l])=>`<button class="${k==tab?'on':''}" onclick="go('${k}')">${l}</button>`).join('');
$('#view').innerHTML={ringkasan:vRingkasan,sewa:vSewa,barang:vBarang,pelanggan:vPelanggan}[tab]();}
function badge(st){return`<span class="bd ${st[1]}">${st[0]}</span>`}
function rentRows(list,withAct){return list.length?list.map(r=>{const c=calc(r),it=item(r.itemId),cu=cust(r.custId);
return`<tr><td><div class="pp">${th(fb(it),it?.nama)}<div><b>${esc(it?.nama)}</b><br><span class="mute">${esc(it?.kode)}</span></div></div></td><td><div class="pp">${th(fp(cu),cu?.nama)}<div>${esc(cu?.nama)}<br><span class="mute">${esc(cu?.hp)}</span></div></div></td><td>${fd(r.start)}</td><td>${fd(r.due)}</td><td>${fd(r.returned)}</td><td>${badge(c.st)}</td><td class="n">${c.late?c.late+' hari':'—'}</td><td class="n">${rp(c.fine)}</td><td class="n"><b>${rp(c.total)}</b></td>${withAct?`<td><div class="act">${r.returned?'':`<button class="btn s" onclick="retForm(${r.id})">Kembalikan</button>`}<button class="btn g s" onclick="delRental(${r.id})">Hapus</button></div></td>`:''}</tr>`}).join(''):`<tr><td colspan="10" class="emp">Belum ada data penyewaan.</td></tr>`}
const rentHead=a=>`<thead><tr><th>Barang</th><th>Pelanggan</th><th>Tgl sewa</th><th>Jatuh tempo</th><th>Tgl kembali</th><th>Status</th><th class="n">Telat</th><th class="n">Denda</th><th class="n">Total</th>${a?'<th></th>':''}</tr></thead>`;
function vRingkasan(){
const run=S.rentals.filter(r=>!r.returned),late=run.filter(r=>calc(r).late>0),done=S.rentals.filter(r=>r.returned);
const fineRun=late.reduce((a,r)=>a+calc(r).fine,0),income=done.reduce((a,r)=>a+calc(r).total,0);
return`<div class="stats">
<div class="st"><b>${S.items.length-run.length}/${S.items.length}</b><span>Barang tersedia</span></div>
<div class="st"><b>${run.length}</b><span>Sedang disewa</span></div>
<div class="st ${late.length?'bad':''}"><b>${late.length}</b><span>Terlambat</span></div>
<div class="st ${fineRun?'bad':''}"><b>${rp(fineRun)}</b><span>Denda berjalan</span></div>
<div class="st"><b>${rp(income)}</b><span>Pendapatan selesai</span></div></div>
<h2>Perlu ditagih sekarang</h2>
<div class="tw"><table>${rentHead(true)}<tbody>${late.length?rentRows(late,true):`<tr><td colspan="10" class="emp">Tidak ada keterlambatan.</td></tr>`}</tbody></table></div>
<h2>Cadangan data</h2>
<p class="mute" style="margin-top:0">Data tersimpan di database MySQL. Ekspor JSON ini untuk arsip; cadangan lengkap lewat phpMyAdmin (Export).</p>
<div class="bar"><button class="btn g" onclick="exp()">Ekspor data</button><button class="btn g" onclick="resetAll()">Hapus semua data</button></div>`}
function vSewa(){
let l=[...S.rentals].sort((a,b)=>b.start.localeCompare(a.start));
if(flt!='semua')l=l.filter(r=>{const c=calc(r);return flt=='berjalan'?!r.returned:flt=='terlambat'?(!r.returned&&c.late>0):!!r.returned});
return`<div class="bar"><button class="btn" onclick="rentForm()">Sewa baru</button><span class="sp"></span><select onchange="flt=this.value;render()" aria-label="Filter status">${[['semua','Semua'],['berjalan','Berjalan'],['terlambat','Terlambat'],['selesai','Selesai']].map(([k,t])=>`<option value="${k}" ${k==flt?'selected':''}>${t}</option>`).join('')}</select></div>
<div class="tw"><table>${rentHead(true)}<tbody>${rentRows(l,true)}</tbody></table></div>`}
function vBarang(){
return`<div class="bar"><button class="btn" onclick="itemForm()">Tambah barang</button><span class="mute">Jenis barang bebas: laptop, printer, proyektor, kamera, dll.</span></div>
<div class="tw"><table><thead><tr><th></th><th>Kode</th><th>Nama</th><th>Jenis</th><th class="n">Tarif/hari</th><th class="n">Denda/hari</th><th>Status</th><th></th></tr></thead><tbody>${S.items.length?S.items.map(i=>{const a=active(i.id);return`<tr><td>${th(fb(i),i.nama)}</td><td>${esc(i.kode)}</td><td><b>${esc(i.nama)}</b>${i.cat?`<br><span class="mute">${esc(i.cat)}</span>`:''}</td><td>${esc(i.kat)}</td><td class="n">${rp(i.rate)}</td><td class="n">${rp(i.fine)}</td><td>${a?`<span class="bd warn">Disewa</span><br><span class="mute">${esc(cust(a.custId)?.nama)}</span>`:'<span class="bd ok">Tersedia</span>'}</td><td><div class="act"><button class="btn g s" onclick="itemForm(${i.id})">Ubah</button><button class="btn g s" onclick="delItem(${i.id})">Hapus</button></div></td></tr>`}).join(''):`<tr><td colspan="8" class="emp">Belum ada barang. Tambahkan barang pertama.</td></tr>`}</tbody></table></div>`}
function vPelanggan(){
return`<div class="bar"><button class="btn" onclick="custForm()">Tambah pelanggan</button></div>
<div class="tw"><table><thead><tr><th></th><th>Nama</th><th>Telepon</th><th>No. identitas</th><th>Alamat</th><th class="n">Sewa aktif</th><th></th></tr></thead><tbody>${S.customers.length?S.customers.map(c=>`<tr><td>${th(fp(c),c.nama)}</td><td><b>${esc(c.nama)}</b></td><td>${esc(c.hp)}</td><td>${esc(c.idn)}</td><td>${esc(c.alamat)}</td><td class="n">${S.rentals.filter(r=>r.custId==c.id&&!r.returned).length}</td><td><div class="act"><button class="btn g s" onclick="custForm(${c.id})">Ubah</button><button class="btn g s" onclick="delCust(${c.id})">Hapus</button></div></td></tr>`).join(''):`<tr><td colspan="7" class="emp">Belum ada pelanggan. Tambahkan pelanggan pertama.</td></tr>`}</tbody></table></div>`}
/* foto */
let ft={d:null,del:false};
const fb=i=>i&&i.foto?'uploads/barang/'+i.foto:'';
const fp=c=>c&&c.foto?'foto.php?f='+c.foto:'';
const th=(src,nm)=>src?`<img class="th" src="${esc(src)}" alt="" loading="lazy" onclick="zoom(this.src)">`:`<span class="th ph">${esc((nm||'?').charAt(0).toUpperCase())}</span>`;
function zoom(s){modal('Foto',`<img src="${esc(s)}" alt="" style="max-width:100%;border-radius:8px">`,'',null);$('#cx').textContent='Tutup'}
function readImg(file,max=800){return new Promise((ok,no)=>{const u=URL.createObjectURL(file),im=new Image();im.onload=()=>{const k=Math.min(1,max/Math.max(im.width,im.height)),c=document.createElement('canvas');c.width=Math.round(im.width*k);c.height=Math.round(im.height*k);const x=c.getContext('2d');x.fillStyle='#fff';x.fillRect(0,0,c.width,c.height);x.drawImage(im,0,0,c.width,c.height);URL.revokeObjectURL(u);ok(c.toDataURL('image/jpeg',.82))};im.onerror=()=>no(new Error('Berkas bukan gambar.'));im.src=u})}
const fotoField=cur=>`<label for="ff">Foto</label><div class="fp"><img id="fv" class="th big" ${cur?`src="${esc(cur)}"`:'hidden'} alt=""><div><input id="ff" type="file" accept="image/*" onchange="pickFoto(this)"><button type="button" class="btn g s" id="fx" onclick="clrFoto()" ${cur?'':'hidden'}>Hapus foto</button></div></div>`;
async function pickFoto(el){const f=el.files[0];if(!f)return;try{ft.d=await readImg(f);ft.del=false;$('#fv').src=ft.d;$('#fv').hidden=false;$('#fx').hidden=false;$('#er').textContent=''}catch(e){$('#er').textContent=e.message;el.value=''}}
function clrFoto(){ft={d:null,del:true};$('#ff').value='';$('#fv').hidden=true;$('#fx').hidden=true}
/* dialog */
let onOk=null;
function modal(title,body,okText,fn){$('#dt').textContent=title;$('#db').innerHTML=body;$('#er').textContent='';$('#ok').textContent=okText;$('#ok').style.display=okText?'':'none';$('#cx').textContent='Batal';onOk=fn;$('#dlg').showModal();const f=$('#db input,#db select');if(f)f.focus()}
$('#ok').onclick=async()=>{try{const e=onOk&&await onOk();if(e){$('#er').textContent=e;return}await load();$('#dlg').close();render()}catch(x){$('#er').textContent=x.message}};
$('#cx').onclick=()=>$('#dlg').close();
function ask(msg,fn,btn='Hapus'){modal('Konfirmasi',`<p>${esc(msg)}</p>`,btn,async()=>{await fn()})}
const fld=(id,l,val='',t='text',x='')=>`<label for="${id}">${l}</label><input id="${id}" type="${t}" value="${esc(val)}" ${x}>`;
/* barang */
function itemForm(id){ft={d:null,del:false};const i=id?item(id):{kode:'',nama:'',kat:'',rate:'',fine:'',cat:''};
const cats=[...new Set(['Laptop','Printer','Proyektor',...S.items.map(x=>x.kat)])];
modal(id?'Ubah barang':'Tambah barang',`<div class="row">${fld('f1','Kode',i.kode)}${fld('f2','Jenis',i.kat,'text','list="kl"')}</div><datalist id="kl">${cats.map(c=>`<option value="${esc(c)}">`).join('')}</datalist>${fld('f3','Nama barang',i.nama)}<div class="row">${fld('f4','Tarif sewa per hari (Rp)',i.rate,'number','min="0"')}${fld('f5','Denda per hari terlambat (Rp)',i.fine,'number','min="0"')}</div>${fld('f6','Catatan (kondisi, kelengkapan)',i.cat)}${fotoField(fb(i))}`,'Simpan',async()=>{
if(!v('f3'))return'Nama barang wajib diisi.';if(!v('f2'))return'Jenis barang wajib diisi.';
const o={kode:v('f1'),kat:v('f2'),nama:v('f3'),rate:+v('f4')||0,fine:+v('f5')||0,cat:v('f6')};
await api(id?'item_update':'item_add',{id,...o,foto:ft.d,hapus_foto:ft.del})})}
function delItem(id){if(S.rentals.some(r=>r.itemId==id))return modal('Tidak bisa dihapus','<p>Barang ini punya riwayat penyewaan. Hapus riwayatnya terlebih dahulu jika ingin menghapus barang.</p>','',null),$('#ok').style.display='none';$('#ok').style.display='';ask('Hapus barang ini?',()=>api('item_delete',{id}))}
/* pelanggan */
function custForm(id){ft={d:null,del:false};const c=id?cust(id):{nama:'',hp:'',idn:'',alamat:''};
modal(id?'Ubah pelanggan':'Tambah pelanggan',fld('c1','Nama',c.nama)+`<div class="row">${fld('c2','Telepon',c.hp,'tel')}${fld('c3','No. identitas (NIK/NIS)',c.idn)}</div>`+fld('c4','Alamat',c.alamat)+fotoField(fp(c)),'Simpan',async()=>{
if(!v('c1'))return'Nama wajib diisi.';const o={nama:v('c1'),hp:v('c2'),idn:v('c3'),alamat:v('c4')};
await api(id?'cust_update':'cust_add',{id,...o,foto:ft.d,hapus_foto:ft.del})})}
function delCust(id){if(S.rentals.some(r=>r.custId==id))return modal('Tidak bisa dihapus','<p>Pelanggan ini punya riwayat penyewaan. Hapus riwayatnya terlebih dahulu.</p>','',null),$('#ok').style.display='none';$('#ok').style.display='';ask('Hapus pelanggan ini?',()=>api('cust_delete',{id}))}
/* sewa */
function rentForm(){
const av=S.items.filter(i=>!active(i.id));
if(!S.customers.length||!av.length){modal('Belum bisa menyewakan',`<p>${!S.customers.length?'Tambahkan pelanggan terlebih dahulu. ':''}${!av.length?'Tidak ada barang yang tersedia.':''}</p>`,'',null);$('#ok').style.display='none';return}
$('#ok').style.display='';
modal('Sewa baru',`<label for="r1">Pelanggan</label><select id="r1">${S.customers.map(c=>`<option value="${c.id}">${esc(c.nama)}</option>`).join('')}</select><label for="r2">Barang (hanya yang tersedia)</label><select id="r2" onchange="rPv()">${av.map(i=>`<option value="${i.id}">${esc(i.kode)} · ${esc(i.nama)}</option>`).join('')}</select><div class="row">${fld('r3','Tanggal sewa',today(),'date','oninput="rPv()"')}${fld('r4','Tanggal kembali (jatuh tempo)',add(today(),3),'date','oninput="rPv()"')}</div><div class="pv" id="pv"></div>`,'Simpan sewa',async()=>{
if(!v('r3')||!v('r4'))return'Tanggal wajib diisi.';if(diff(v('r3'),v('r4'))<0)return'Tanggal kembali tidak boleh sebelum tanggal sewa.';
await api('rent_add',{itemId:+v('r2'),custId:+v('r1'),start:v('r3'),due:v('r4')});flt='semua';tab='sewa'});rPv()}
function rPv(){const i=item(v('r2')),a=v('r3'),b=v('r4');if(!i||!a||!b)return;const d=Math.max(1,diff(a,b));$('#pv').innerHTML=`${d} hari × ${rp(i.rate)} = <b>${rp(d*i.rate)}</b><br><span class="mute">Denda jika terlambat: ${rp(i.fine)} per hari</span>`}
function retForm(id){const r=S.rentals.find(x=>x.id==id);
modal('Kembalikan barang',`<p style="margin:0"><b>${esc(item(r.itemId)?.nama)}</b> · ${esc(cust(r.custId)?.nama)}<br><span class="mute">Disewa ${fd(r.start)}, jatuh tempo ${fd(r.due)}</span></p>${fld('b1','Tanggal kembali',today(),'date','oninput="bPv('+id+')"')}<div class="pv" id="pv"></div>`,'Tandai dikembalikan',async()=>{
if(!v('b1'))return'Tanggal kembali wajib diisi.';if(diff(r.start,v('b1'))<0)return'Tanggal kembali tidak boleh sebelum tanggal sewa.';await api('rent_return',{id,returned:v('b1')})});bPv(id)}
function bPv(id){const r={...S.rentals.find(x=>x.id==id),returned:v('b1')||today()},c=calc(r);
$('#pv').innerHTML=c.late?`Terlambat <b>${c.late} hari</b><br>Sewa ${rp(c.rent)} + denda ${rp(c.fine)}<br>Total tagihan <b>${rp(c.total)}</b>`:`Tepat waktu<br>Total tagihan <b>${rp(c.total)}</b>`}
function delRental(id){ask('Hapus catatan penyewaan ini?',()=>api('rent_delete',{id}))}
/* data */
function exp(){const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([JSON.stringify(S,null,1)],{type:'application/json'}));a.download='data-sewa-'+today()+'.json';a.click()}
function resetAll(){$('#ok').style.display='';ask('Hapus semua barang, pelanggan, dan penyewaan? Tindakan ini tidak bisa dibatalkan.',()=>api('reset',{}),'Hapus semua')}
load().then(render).catch(e=>{$('#view').innerHTML='<p class="emp">Gagal memuat data: '+esc(e.message)+'<br>Pastikan Apache dan MySQL di XAMPP aktif dan database sudah diimpor.</p>'});
</script>
</body>
</html>
