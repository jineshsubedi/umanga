<script setup>
import { ref, computed } from 'vue';
import NepaliDate from 'nepali-date';

const props = defineProps({
    memos:           { type: Array,  default: () => [] },
    procurements:    { type: Array,  default: () => [] },
});
const emit = defineEmits(['date-click']);

const todayNp   = new NepaliDate();
const today     = new NepaliDate();
const curYear   = ref(today.getYear());
const curMonth  = ref(today.getMonth()); // 0-indexed

const NP_MONTHS = ['Baisakh','Jestha','Ashadh','Shrawan','Bhadra','Ashwin','Kartik','Mangsir','Poush','Magh','Falgun','Chaitra'];
const NP_MONTHS_NP = ['वैशाख','जेठ','असार','साउन','भदौ','असोज','कात्तिक','मंसिर','पुष','माघ','फागुन','चैत'];
const DAYS_EN = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
const DAYS_NP = ['आइत','सोम','मंगल','बुध','बिही','शुक्र','शनि'];
const NP_DIGITS = ['०','१','२','३','४','५','६','७','८','९'];

const toNp = n => String(n).split('').map(d=>NP_DIGITS[+d]??d).join('');

const toDateStr = d => {
    if (!d) return '';
    if (typeof d === 'string') return d.substring(0,10);
    if (d instanceof Date) return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    return '';
};

const todayStr = computed(()=>toDateStr(new Date()));

const prevMonth=()=>{ if(curMonth.value===0){curMonth.value=11;curYear.value--;}else curMonth.value--; };
const nextMonth=()=>{ if(curMonth.value===11){curMonth.value=0;curYear.value++;}else curMonth.value++; };
const goToToday=()=>{ curYear.value=todayNp.getYear(); curMonth.value=todayNp.getMonth(); };

const daysInMonth = computed(()=>{
    let n=28;
    for(let i=29;i<=32;i++){
        try{ const d=new NepaliDate(curYear.value,curMonth.value,i); if(d.getMonth()===curMonth.value) n=i; else break; }catch{ break; }
    }
    return n;
});

const calendarGrid = computed(()=>{
    const first = new NepaliDate(curYear.value,curMonth.value,1);
    const startDow = first.getDay();
    const grid = [];
    for(let i=0;i<startDow;i++) grid.push({empty:true});
    for(let d=1;d<=daysInMonth.value;d++){
        const nd = new NepaliDate(curYear.value,curMonth.value,d);
        const ed = nd.getEnglishDate();
        if(!ed){ grid.push({empty:true}); continue; }
        const edObj = new Date(ed);
        const dateStr=`${edObj.getFullYear()}-${String(edObj.getMonth()+1).padStart(2,'0')}-${String(edObj.getDate()).padStart(2,'0')}`;
        grid.push({ empty:false, npDate:d, npDateStr:toNp(d), dow:nd.getDay(), isSat:nd.getDay()===6, englishDateStr:dateStr, engDay:edObj.getDate(), engMonStr:edObj.toLocaleString('en-US',{month:'short'}) });
    }
    return grid;
});

const enRange = computed(()=>{
    const days=calendarGrid.value.filter(g=>!g.empty);
    if(!days.length) return '';
    const f=days[0], l=days[days.length-1];
    const y1=new Date(f.englishDateStr).getFullYear(), y2=new Date(l.englishDateStr).getFullYear();
    return f.engMonStr===l.engMonStr&&y1===y2 ? `${f.engMonStr} ${y1}` : `${f.engMonStr}/${l.engMonStr} ${y1===y2?y1:y1+'/'+y2}`;
});

const selectedDateStr = ref(todayStr.value);

const memosByDate = computed(()=>{
    const map={};
    props.memos.forEach(m=>{ const s=toDateStr(m.meeting_date||m.date); if(s){ (map[s]=map[s]||[]).push(m); } });
    return map;
});

const procurementsByDate = computed(()=>{
    const map={};
    props.procurements.forEach(p=>{ const s=toDateStr(p.date||p.created_at); if(s){ (map[s]=map[s]||[]).push(p); } });
    return map;
});

const getDayInfo = (dateStr)=>{
    const mems = memosByDate.value[dateStr]||[];
    const procs = procurementsByDate.value[dateStr]||[];
    return { memos:mems, procurements:procs };
};

const selectedInfo = computed(()=> getDayInfo(selectedDateStr.value));

const selectedDateLabel = computed(()=>{
    if(!selectedDateStr.value) return '';
    const d=new Date(selectedDateStr.value);
    return d.toLocaleDateString(undefined,{weekday:'long',year:'numeric',month:'long',day:'numeric'});
});

const onDayClick=(dateStr)=>{ selectedDateStr.value=dateStr; emit('date-click',dateStr); };

const statusColors={
    pending:'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300',
    Pending:'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300',
    approved:'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300',
    Approved:'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300',
    rejected:'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300',
    Rejected:'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300',
    Returned:'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-300',
    draft:'bg-gray-50 dark:bg-gray-700/30 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300',
};
const badgeDot={ 
    pending:'bg-amber-400', Pending:'bg-amber-400',
    approved:'bg-blue-500', Approved:'bg-blue-500',
    rejected:'bg-red-400', Rejected:'bg-red-400',
    Returned:'bg-orange-400', draft:'bg-gray-400' 
};
</script>

<template>
<div class="space-y-4">
    <!-- ── Calendar Card ───────────────────────────────────────────── -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ NP_MONTHS[curMonth] }}</p>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">
                        {{ NP_MONTHS_NP[curMonth] }} {{ toNp(curYear) }}
                    </h2>
                </div>
                <span class="text-sm text-gray-400 dark:text-gray-500 border-l border-gray-200 dark:border-gray-700 pl-3 hidden sm:block">{{ enRange }}</span>
            </div>
            <div class="flex items-center gap-2">
                <button @click="goToToday" class="px-3 py-1.5 bg-[#ff6b2b] hover:bg-[#e55a1e] text-white text-xs font-bold rounded-lg shadow transition-colors">
                    आज / Today
                </button>
                <div class="flex bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm">
                    <button @click="prevMonth" class="px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-colors border-r border-gray-200 dark:border-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="nextMonth" class="px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Day Headers -->
        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
            <div v-for="(d,i) in DAYS_EN" :key="d"
                class="py-2 text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0"
                :class="i===6?'text-red-500 dark:text-red-400':'text-gray-500 dark:text-gray-400'">
                <p class="text-[11px] font-bold uppercase">{{ DAYS_NP[i] }}</p>
                <p class="text-[10px] opacity-70">{{ d }}</p>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-7 dark:bg-gray-700" style="gap:1px;background:#e5e7eb;">
            <div v-for="(day,idx) in calendarGrid" :key="idx"
                class="relative min-h-[120px] md:min-h-[130px] bg-white dark:bg-gray-800 flex flex-col p-1.5 group transition-colors animate-fade-in"
                :class="{
                    'opacity-0 pointer-events-none':day.empty,
                    'bg-orange-50 dark:bg-orange-900/10':!day.empty&&day.englishDateStr===todayStr,
                    'bg-purple-50/50 dark:bg-purple-900/10':!day.empty&&day.englishDateStr===selectedDateStr&&day.englishDateStr!==todayStr,
                    'hover:bg-gray-50 dark:hover:bg-gray-700/50':!day.empty&&day.englishDateStr!==todayStr,
                }"
                @click="!day.empty&&onDayClick(day.englishDateStr)">

                <template v-if="!day.empty">
                    <!-- Date numbers -->
                    <div class="flex items-start justify-between">
                        <span class="text-2xl md:text-3xl font-light leading-none mt-0.5"
                            :class="[
                                day.isSat?'text-red-500 dark:text-red-400':'text-gray-800 dark:text-gray-100',
                                day.englishDateStr===todayStr?'font-bold':''
                            ]">{{ day.npDateStr }}</span>
                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 mt-0.5">{{ day.engDay }}</span>
                    </div>

                    <!-- Today ring -->
                    <div v-if="day.englishDateStr===todayStr" class="absolute top-1 left-1 w-7 h-7 rounded-full border-2 border-orange-400 pointer-events-none"></div>

                    <!-- Badges (Memos & Procurements) -->
                    <div class="mt-auto space-y-0.5 z-10">
                        <div v-if="getDayInfo(day.englishDateStr).memos.length>0"
                            class="flex items-center gap-1 text-[9px] md:text-[10px] px-1 py-0.5 rounded bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 font-bold border border-purple-100 dark:border-purple-800/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500 flex-shrink-0"></span>
                            {{ getDayInfo(day.englishDateStr).memos.length }} Memo{{ getDayInfo(day.englishDateStr).memos.length>1?'s':'' }}
                        </div>
                        <div v-if="getDayInfo(day.englishDateStr).procurements.length>0"
                            class="flex items-center gap-1 text-[9px] md:text-[10px] px-1 py-0.5 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-100 dark:border-emerald-800/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                            {{ getDayInfo(day.englishDateStr).procurements.length }} Request{{ getDayInfo(day.englishDateStr).procurements.length>1?'s':'' }}
                        </div>
                    </div>

                    <!-- Click cursor -->
                    <div class="absolute inset-0 cursor-pointer"></div>
                </template>
            </div>
        </div>

        <!-- Legend -->
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-x-5 gap-y-1.5 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span> Memos
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Procurement Requests
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span> Today
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-red-500 dark:text-red-400">
                <span class="w-2 h-2 rounded-full bg-red-400"></span> Saturday
            </div>
        </div>
    </div>

    <!-- ── Day Detail Panel ────────────────────────────────────────── -->
    <div v-if="selectedDateStr" class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 dark:border-gray-700 overflow-hidden transition-all duration-300">

        <!-- Panel header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 mb-0.5">
                    {{ selectedDateStr===todayStr ? 'Today' : 'Selected Date' }}
                </p>
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">{{ selectedDateLabel }}</h3>
            </div>
            <span v-if="selectedDateStr===todayStr" class="px-3 py-1 bg-gradient-to-r from-[#7c3aed] to-[#b026ff] text-white text-xs font-bold rounded-lg shadow-sm">Today</span>
        </div>

        <div class="p-6 grid md:grid-cols-2 gap-6">

            <!-- Procurement Requests Section -->
            <div>
                <h4 class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Procurement Requests
                    <span class="ml-auto px-2 py-0.5 text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 rounded-lg">{{ selectedInfo.procurements?.length||0 }}</span>
                </h4>
                <div v-if="selectedInfo.procurements?.length>0" class="space-y-2">
                    <div v-for="proc in selectedInfo.procurements" :key="proc.id"
                        class="flex items-center justify-between p-3 rounded-xl border transition-all hover:shadow-md"
                        :class="statusColors[proc.status]||statusColors.draft">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" :class="badgeDot[proc.status]||'bg-gray-400'"></span>
                            <div class="truncate">
                                <p class="text-sm font-bold truncate">{{ proc.item_name }}</p>
                                <p class="text-[10px] opacity-75 truncate">Qty: {{ proc.quantity }} • Est. Cost: रू {{ proc.estimated_cost }}</p>
                            </div>
                        </div>
                        <span class="ml-2 flex-shrink-0 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border" :class="statusColors[proc.status]||statusColors.draft">
                            {{ proc.status }}
                        </span>
                    </div>
                </div>
                <div v-else class="flex flex-col items-center justify-center p-8 bg-gray-50 dark:bg-gray-700/20 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">No procurement requests for this day</p>
                </div>
            </div>

            <!-- Memos Section -->
            <div>
                <h4 class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Memos
                    <span class="ml-auto px-2 py-0.5 text-[10px] font-bold bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 rounded-lg">{{ selectedInfo.memos?.length||0 }}</span>
                </h4>
                <div v-if="selectedInfo.memos?.length>0" class="space-y-2">
                    <div v-for="memo in selectedInfo.memos" :key="memo.id"
                        class="flex items-center justify-between p-3 rounded-xl border transition-all hover:shadow-md"
                        :class="statusColors[memo.status]||statusColors.draft">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" :class="badgeDot[memo.status]||'bg-gray-400'"></span>
                            <p class="text-sm font-bold truncate">{{ memo.title }}</p>
                        </div>
                        <span class="ml-2 flex-shrink-0 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border" :class="statusColors[memo.status]||statusColors.draft">
                            {{ memo.status }}
                        </span>
                    </div>
                </div>
                <div v-else class="flex flex-col items-center justify-center p-8 bg-gray-50 dark:bg-gray-700/20 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">No memos for this day</p>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<style scoped>
.grid[style*="gap:1px"] { gap: 1px; }
</style>
