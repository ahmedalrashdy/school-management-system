<x-layouts.dashboard page-title="تحليل الحضور والغياب الذكي (ApexCharts)">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-300">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
                    x-data="attendanceCharts(@js($chartData))" x-init="init()" x-cloak>

                    {{-- Header & Controls --}}
                    <div class="mb-8 space-y-4">
                        <div
                            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-100 dark:border-gray-700 pb-4">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">لوحة التحكم والمؤشرات
                                (ApexCharts)</h2>

                            {{-- Chart Type Selector --}}
                            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                <button @click="setChartType('bar')"
                                    :class="{'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400': chartType === 'bar', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': chartType !== 'bar'}"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center">
                                    <i class="fas fa-chart-bar me-2"></i>أعمدة
                                </button>
                                <button @click="setChartType('pie')"
                                    :class="{'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400': chartType === 'pie', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': chartType !== 'pie'}"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center">
                                    <i class="fas fa-chart-pie me-2"></i>دائري
                                </button>
                                <button @click="setChartType('line')"
                                    :class="{'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400': chartType === 'line', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': chartType !== 'line'}"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center">
                                    <i class="fas fa-chart-line me-2"></i>اتجاه زمني
                                </button>
                            </div>
                        </div>

                        {{-- Modern Filters --}}
                        <div
                            class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                            {{-- Stage Filter --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">المرحلة
                                    الدراسية</label>
                                <select x-model="filters.stage_id" @change="handleFilterChange('stage')"
                                    class="w-full border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm appearance-none cursor-pointer">
                                    <option value="">جميع المراحل</option>
                                    <template x-for="stage in getStages()" :key="stage.id">
                                        <option :value="stage.id" x-text="stage.name"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Grade Filter --}}
                            <div :class="{'opacity-50 pointer-events-none': !filters.stage_id}">
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">الصف
                                    الدراسي</label>
                                <select x-model="filters.grade_id" @change="handleFilterChange('grade')"
                                    class="w-full border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm appearance-none cursor-pointer">
                                    <option value="">جميع الصفوف</option>
                                    <template x-for="grade in getGrades()" :key="grade.id">
                                        <option :value="grade.id" x-text="grade.name"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Section Filter --}}
                            <div :class="{'opacity-50 pointer-events-none': !filters.grade_id}">
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">الشعبة</label>
                                <select x-model="filters.section_id" @change="handleFilterChange('section')"
                                    class="w-full border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm appearance-none cursor-pointer">
                                    <option value="">جميع الشعب</option>
                                    <template x-for="section in getSections()" :key="section.id">
                                        <option :value="section.id" x-text="section.name"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Reset Button --}}
                            <div class="flex items-end">
                                <button @click="resetFilters()"
                                    class="w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-undo me-2"></i>إعادة تعيين
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Charts Area --}}
                    <div class="min-h-[400px] relative">
                        {{-- Standard View (Bar/Line) --}}
                        <div x-show="chartType !== 'pie'" class="w-full h-96 transition-all duration-300 p-2">
                            {{-- ApexCharts renders into DIVs --}}
                            <div id="mainChart"></div>
                        </div>

                        {{-- Pie Grid View --}}
                        <div x-show="chartType === 'pie'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-for="(item, index) in pieDataItems" :key="index">
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center hover:shadow-md transition-shadow">
                                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-4 truncate w-full text-center"
                                        x-text="item.label"></h4>
                                    <div class="w-full flex justify-center">
                                        {{-- ApexPie --}}
                                        <div :id="'pieChart_' + index"></div>
                                    </div>
                                </div>
                            </template>
                            {{-- Empty State for Pie --}}
                            <div x-show="pieDataItems.length === 0"
                                class="col-span-full text-center text-gray-400 dark:text-gray-500 py-10">
                                لا توجد بيانات لعرضها
                            </div>
                        </div>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                        <template x-for="statusKey in Object.keys(statusMap)" :key="statusKey">
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-200 dark:border-gray-600 text-center transform transition duration-200 hover:scale-105 hover:shadow-md cursor-default"
                                x-show="globalStats[statusKey] > 0">
                                <div class="text-[10px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-1"
                                    x-text="statusMap[statusKey].label"></div>
                                <div class="text-2xl font-black" :style="'color: ' + statusMap[statusKey].color"
                                    x-text="globalStats[statusKey]"></div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- ApexCharts CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('attendanceCharts', (rawData) => {
                    const STATUS_MAP = {
                        '1': { label: 'حاضر', color: '#10B981' },
                        '2': { label: 'غائب', color: '#EF4444' },
                        '3': { label: 'متأخر', color: '#F59E0B' },
                        '4': { label: 'معذور', color: '#3B82F6' },
                        'partial_absence': { label: 'غياب جزئي', color: '#F87171' },
                        'present_with_late': { label: 'حضور مع تأخير', color: '#FCD34D' },
                        'partial_excused': { label: 'عذر جزئي', color: '#93C5FD' }
                    };

                    let mainChart = null;
                    let pieCharts = [];

                    const isDarkMode = () => document.documentElement.classList.contains('dark') ||
                        document.querySelector('html').classList.contains('dark');

                    return {
                        rawData: rawData,
                        chartType: 'bar',
                        statusMap: STATUS_MAP,

                        filters: {
                            stage_id: '',
                            grade_id: '',
                            section_id: ''
                        },

                        pieDataItems: [],

                        init() {
                            this.$watch('filters', () => this.renderCharts());
                            this.renderCharts();
                        },

                        setChartType(type) {
                            this.chartType = type;
                            setTimeout(() => this.renderCharts(), 50);
                        },

                        handleFilterChange(level) {
                            if (level === 'stage') {
                                this.filters.grade_id = '';
                                this.filters.section_id = '';
                            } else if (level === 'grade') {
                                this.filters.section_id = '';
                            }
                        },

                        resetFilters() {
                            this.filters = { stage_id: '', grade_id: '', section_id: '' };
                        },

                        getFilteredData() {
                            return this.rawData.filter(r => {
                                if (this.filters.stage_id && r.meta.stage_id != this.filters.stage_id) return false;
                                if (this.filters.grade_id && r.meta.grade_id != this.filters.grade_id) return false;
                                if (this.filters.section_id && r.section_id != this.filters.section_id) return false;
                                return true;
                            });
                        },

                        getCurrentHierarchyLevel() {
                            if (this.filters.section_id) return 'section';
                            if (this.filters.grade_id) return 'section';
                            if (this.filters.stage_id) return 'grade';
                            return 'stage';
                        },

                        getUnique(key, labelKey, filterFn = () => true) {
                            const seen = new Set();
                            return this.rawData
                                .filter(filterFn)
                                .filter(r => {
                                    const val = r.meta[key] || r[key];
                                    if (seen.has(val)) return false;
                                    seen.add(val);
                                    return true;
                                })
                                .map(r => ({
                                    id: r.meta[key] || r[key],
                                    name: r.meta[labelKey] || r[key + '_name'] || ('ID: ' + r[key])
                                }))
                                .sort((a, b) => a.id - b.id);
                        },

                        getStages() { return this.getUnique('stage_id', 'stage_name'); },
                        getGrades() { return this.getUnique('grade_id', 'grade_name', r => !this.filters.stage_id || r.meta.stage_id == this.filters.stage_id); },

                        getSections() {
                            const seen = new Set();
                            let filtered = this.rawData;
                            if (this.filters.grade_id) filtered = filtered.filter(r => r.meta.grade_id == this.filters.grade_id);
                            return filtered.reduce((acc, r) => {
                                if (!seen.has(r.section_id)) {
                                    seen.add(r.section_id);
                                    acc.push({ id: r.section_id, name: r.meta.section_name });
                                }
                                return acc;
                            }, []).sort((a, b) => a.name.localeCompare(b.name));
                        },

                        renderCharts() {
                            if (mainChart) { mainChart.destroy(); mainChart = null; }
                            pieCharts.forEach(c => c.destroy());
                            pieCharts = [];

                            const data = this.getFilteredData();

                            if (this.chartType === 'line') {
                                this.renderTrendChart(data);
                            } else if (this.chartType === 'pie') {
                                this.renderPieCharts(data, this.getCurrentHierarchyLevel());
                            } else {
                                this.renderBarChart(data, this.getCurrentHierarchyLevel());
                            }
                        },

                        groupBy(data, level) {
                            const groups = {};
                            data.forEach(item => {
                                let key, label;
                                if (level === 'stage') {
                                    key = item.meta.stage_id;
                                    label = item.meta.stage_name;
                                } else if (level === 'grade') {
                                    key = item.meta.grade_id;
                                    label = item.meta.grade_name;
                                } else {
                                    key = item.section_id;
                                    label = item.meta.section_name;
                                }
                                if (!groups[key]) groups[key] = { label, items: [] };
                                groups[key].items.push(item);
                            });
                            return groups;
                        },

                        // --- Generic ApexCharts Helper ---
                        getCommonOptions() {
                            const dark = isDarkMode();
                            return {
                                chart: {
                                    background: 'transparent',
                                    toolbar: { show: false },
                                    fontFamily: 'inherit',
                                },
                                theme: {
                                    mode: dark ? 'dark' : 'light',
                                },
                                dataLabels: { enabled: false },
                                grid: {
                                    borderColor: dark ? '#374151' : '#e5e7eb',
                                    strokeDashArray: 4,
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: { colors: dark ? '#e5e7eb' : '#374151' }
                                },
                                tooltip: { theme: dark ? 'dark' : 'light' }
                            };
                        },

                        renderTrendChart(data) {
                            const datesMap = {};
                            data.forEach(item => {
                                const dateStr = item.date.split('T')[0];
                                if (!datesMap[dateStr]) datesMap[dateStr] = {};
                                Object.keys(this.statusMap).forEach(k => {
                                    datesMap[dateStr][k] = (datesMap[dateStr][k] || 0) + (item.stats[k] || 0);
                                });
                            });
                            const sortedDates = Object.keys(datesMap).sort();

                            // Prepare Series for Apex
                            // One series per status, data is array of values

                            const series = Object.keys(this.statusMap).map(status => {
                                return {
                                    name: this.statusMap[status].label,
                                    data: sortedDates.map(d => datesMap[d][status])
                                }
                            }).filter(s => s.data.some(v => v > 0)); // Filter empty? Apex matches by index, better keep all or ensure categories match

                            const options = {
                                ...this.getCommonOptions(),
                                series: series,
                                chart: {
                                    type: 'line',
                                    height: 350,
                                    background: 'transparent',
                                    toolbar: { show: false }
                                },
                                colors: Object.keys(this.statusMap).map(s => this.statusMap[s].color),
                                xaxis: {
                                    categories: sortedDates,
                                    type: 'datetime',
                                    labels: {
                                        datetimeFormatter: { year: 'yyyy', month: 'dd MMM', day: 'dd MMM' },
                                        style: { colors: isDarkMode() ? '#9ca3af' : '#6b7280' }
                                    },
                                    axisBorder: { show: false },
                                    axisTicks: { show: false }
                                },
                                yaxis: {
                                    labels: {
                                        style: { colors: isDarkMode() ? '#9ca3af' : '#6b7280' }
                                    }
                                },
                                stroke: { curve: 'smooth', width: 3 }
                            };

                            const el = document.querySelector("#mainChart");
                            if (el) {
                                mainChart = new ApexCharts(el, options);
                                mainChart.render();
                            }
                        },

                        renderBarChart(data, level) {
                            const grouped = this.groupBy(data, level);
                            const keys = Object.keys(grouped);
                            const labels = keys.map(k => grouped[k].label);

                            // Series: each status is a series
                            const series = Object.keys(this.statusMap).map(status => {
                                return {
                                    name: this.statusMap[status].label,
                                    data: keys.map(k => grouped[k].items.reduce((sum, item) => sum + (item.stats[status] || 0), 0))
                                };
                            });

                            const options = {
                                ...this.getCommonOptions(),
                                series: series,
                                chart: {
                                    type: 'bar',
                                    height: 350,
                                    stacked: false, // Grouped bars
                                    background: 'transparent',
                                    toolbar: { show: false }
                                },
                                colors: Object.keys(this.statusMap).map(s => this.statusMap[s].color),
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        borderRadius: 4
                                    },
                                },
                                dataLabels: { enabled: false },
                                xaxis: {
                                    categories: labels,
                                    labels: {
                                        style: { colors: isDarkMode() ? '#9ca3af' : '#6b7280' }
                                    },
                                    axisBorder: { show: false },
                                    axisTicks: { show: false }
                                },
                                yaxis: {
                                    labels: {
                                        style: { colors: isDarkMode() ? '#9ca3af' : '#6b7280' }
                                    }
                                }
                            };

                            const el = document.querySelector("#mainChart");
                            if (el) {
                                mainChart = new ApexCharts(el, options);
                                mainChart.render();
                            }
                        },

                        renderPieCharts(data, level) {
                            const grouped = this.groupBy(data, level);
                            this.pieDataItems = Object.keys(grouped).map(k => ({
                                id: k, label: grouped[k].label, items: grouped[k].items
                            }));

                            this.$nextTick(() => {
                                this.pieDataItems.forEach((group, index) => {
                                    const el = document.getElementById('pieChart_' + index);
                                    if (!el) return;

                                    // Aggregate data for this chart
                                    const statusCounts = {};
                                    Object.keys(this.statusMap).forEach(s => statusCounts[s] = 0);
                                    group.items.forEach(item => {
                                        Object.keys(item.stats).forEach(s => {
                                            if (statusCounts[s] !== undefined) statusCounts[s] += item.stats[s];
                                        });
                                    });

                                    const series = [];
                                    const labels = [];
                                    const colors = [];
                                    Object.keys(statusCounts).forEach(s => {
                                        if (statusCounts[s] > 0) {
                                            series.push(statusCounts[s]);
                                            labels.push(this.statusMap[s].label);
                                            colors.push(this.statusMap[s].color);
                                        }
                                    });

                                    if (series.length === 0) return; // Empty chart

                                    const options = {
                                        ...this.getCommonOptions(),
                                        series: series,
                                        labels: labels,
                                        colors: colors,
                                        chart: {
                                            type: 'donut',
                                            height: 250,
                                            background: 'transparent'
                                        },
                                        plotOptions: {
                                            pie: {
                                                donut: {
                                                    size: '65%',
                                                    labels: {
                                                        show: true,
                                                        name: {
                                                            show: true,
                                                            fontSize: '14px',
                                                            fontFamily: 'inherit',
                                                            color: isDarkMode() ? '#9ca3af' : '#6b7280',
                                                            offsetY: -5
                                                        },
                                                        value: {
                                                            show: true,
                                                            fontSize: '20px',
                                                            fontFamily: 'inherit',
                                                            fontWeight: 'bold',
                                                            color: isDarkMode() ? '#f9fafb' : '#111827',
                                                            offsetY: 5,
                                                            formatter: function (val) {
                                                                return val
                                                            }
                                                        },
                                                        total: {
                                                            show: true,
                                                            showAlways: true,
                                                            label: 'الإجمالي',
                                                            fontSize: '12px',
                                                            fontFamily: 'inherit',
                                                            color: isDarkMode() ? '#9ca3af' : '#6b7280',
                                                            formatter: function (w) {
                                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        legend: { show: false }, // Hide legend for small pies to save space
                                        tooltip: { enabled: true }
                                    };

                                    const chart = new ApexCharts(el, options);
                                    chart.render();
                                    pieCharts.push(chart);
                                });
                            });
                        },

                        get globalStats() {
                            const data = this.getFilteredData();
                            const stats = {};
                            Object.keys(this.statusMap).forEach(k => stats[k] = 0);
                            data.forEach(item => {
                                Object.keys(this.statusMap).forEach(s => { if (stats[s] !== undefined) stats[s] += (item.stats[s] || 0); });
                            });
                            return stats;
                        }
                    };
                });
            });
        </script>
    @endpush
</x-layouts.dashboard>
