document.addEventListener('alpine:init', () => {
    Alpine.data('attendanceCharts', (rawData) => {
        const STATUS_MAP = {
            '1': {
                label: 'حاضر',
                color: '#10B981'
            },
            '2': {
                label: 'غائب',
                color: '#EF4444'
            },
            '3': {
                label: 'متأخر',
                color: '#F59E0B'
            },
            '4': {
                label: 'معذور',
                color: '#3B82F6'
            },
            'partial_absence': {
                label: 'غياب جزئي',
                color: '#F87171'
            },
            'present_with_late': {
                label: 'حضور مع تأخير',
                color: '#FCD34D'
            },
            'partial_excused': {
                label: 'عذر جزئي',
                color: '#93C5FD'
            }
        };

        let mainChart = null;
        let pieCharts = [];

        // Helper to check dark mode
        const isDarkMode = () => document.documentElement.classList.contains('dark') ||
            document.querySelector('html').classList.contains('dark');

        const getGridColor = () => isDarkMode() ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const getTextColor = () => isDarkMode() ? '#e5e7eb' : '#374151';

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

                // Watch for theme changes specifically if using a class watcher (optional enhancement)
                // For now, relies on re-render when user switches views

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
                this.filters = {
                    stage_id: '',
                    grade_id: '',
                    section_id: ''
                };
            },

            getFilteredData() {
                return this.rawData.filter(r => {
                    if (this.filters.stage_id && r.meta.stage_id != this.filters.stage_id)
                        return false;
                    if (this.filters.grade_id && r.meta.grade_id != this.filters.grade_id)
                        return false;
                    if (this.filters.section_id && r.section_id != this.filters.section_id)
                        return false;
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

            getStages() {
                return this.getUnique('stage_id', 'stage_name');
            },
            getGrades() {
                return this.getUnique('grade_id', 'grade_name', r => !this.filters.stage_id || r
                    .meta.stage_id == this.filters.stage_id);
            },

            getSections() {
                const seen = new Set();
                let filtered = this.rawData;
                if (this.filters.grade_id) filtered = filtered.filter(r => r.meta.grade_id == this
                    .filters.grade_id);
                return filtered.reduce((acc, r) => {
                    if (!seen.has(r.section_id)) {
                        seen.add(r.section_id);
                        acc.push({
                            id: r.section_id,
                            name: r.meta.section_name
                        });
                    }
                    return acc;
                }, []).sort((a, b) => a.name.localeCompare(b.name));
            },

            renderCharts() {
                if (mainChart) {
                    mainChart.destroy();
                    mainChart = null;
                }
                pieCharts.forEach(c => c.destroy());
                pieCharts = [];

                const data = this.getFilteredData();
                Chart.defaults.color = getTextColor();
                Chart.defaults.borderColor = getGridColor();

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
                    if (!groups[key]) groups[key] = {
                        label,
                        items: []
                    };
                    groups[key].items.push(item);
                });
                return groups;
            },

            renderTrendChart(data) {
                const ctx = document.getElementById('mainCanvas')?.getContext('2d');
                if (!ctx) return;
                const datesMap = {};
                data.forEach(item => {
                    const dateStr = item.date.split('T')[0];
                    if (!datesMap[dateStr]) {
                        datesMap[dateStr] = {};
                        Object.keys(this.statusMap).forEach(k => datesMap[dateStr][k] = 0);
                    }
                    Object.keys(this.statusMap).forEach(status => {
                        datesMap[dateStr][status] += (item.stats[status] || 0);
                    });
                });
                const sortedDates = Object.keys(datesMap).sort();
                const datasets = Object.keys(this.statusMap).map(status => {
                    const points = sortedDates.map(date => datesMap[date][status]);
                    return {
                        label: this.statusMap[status].label,
                        data: points,
                        borderColor: this.statusMap[status].color,
                        backgroundColor: this.statusMap[status].color,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        fill: false
                    };
                }).filter(ds => ds !== null);

                mainChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: sortedDates.map(d => new Date(d).toLocaleDateString(
                            'en-GB', {
                                day: '2-digit',
                                month: '2-digit'
                            })),
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: getGridColor()
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            },

            renderBarChart(data, level) {
                const ctx = document.getElementById('mainCanvas')?.getContext('2d');
                if (!ctx) return;
                const grouped = this.groupBy(data, level);
                const keys = Object.keys(grouped);
                const labels = keys.map(k => grouped[k].label);
                const datasets = Object.keys(this.statusMap).map(status => {
                    return {
                        label: this.statusMap[status].label,
                        data: keys.map(k => grouped[k].items.reduce((sum, item) => sum + (
                            item.stats[status] || 0), 0)),
                        backgroundColor: this.statusMap[status].color,
                        borderColor: this.statusMap[status].color,
                        borderWidth: 0,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    };
                });

                mainChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            x: {
                                stacked: false,
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                stacked: false,
                                beginAtZero: true,
                                grid: {
                                    color: getGridColor()
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            },

            renderPieCharts(data, level) {
                const grouped = this.groupBy(data, level);
                this.pieDataItems = Object.keys(grouped).map(k => ({
                    id: k,
                    label: grouped[k].label,
                    items: grouped[k].items
                }));

                this.$nextTick(() => {
                    this.pieDataItems.forEach((group, index) => {
                        const canvas = document.getElementById('pieCanvas_' +
                            index);
                        if (!canvas) return;

                        // Destroy existing chart if it exists on this canvas
                        const existingChart = Chart.getChart(canvas);
                        if (existingChart) {
                            existingChart.destroy();
                        }

                        const statusCounts = {};
                        Object.keys(this.statusMap).forEach(s => statusCounts[s] =
                            0);
                        group.items.forEach(item => {
                            Object.keys(item.stats).forEach(s => {
                                if (statusCounts[s] !== undefined)
                                    statusCounts[s] += item.stats[
                                        s];
                            });
                        });

                        const chartData = {
                            labels: [],
                            datasets: [{
                                data: [],
                                backgroundColor: [],
                                borderWidth: 2,
                                borderColor: isDarkMode() ? '#1f2937' :
                                    '#ffffff'
                            }]
                        };

                        Object.keys(statusCounts).forEach(s => {
                            if (statusCounts[s] > 0) {
                                chartData.labels.push(this.statusMap[s]
                                    .label);
                                chartData.datasets[0].data.push(
                                    statusCounts[s]);
                                chartData.datasets[0].backgroundColor.push(
                                    this.statusMap[s].color);
                            }
                        });

                        const pieChart = new Chart(canvas.getContext('2d'), {
                            type: 'doughnut',
                            data: chartData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '65%',
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            },
                            plugins: [{
                                id: 'textCenter',
                                afterDraw: function(chart) {
                                    const width = chart.width,
                                        height = chart.height,
                                        ctx = chart.ctx;
                                    ctx.restore();
                                    const fontSize = (height /
                                        114).toFixed(2);
                                    ctx.font = "bold " +
                                        fontSize +
                                        "em sans-serif";
                                    ctx.textBaseline = "middle";
                                    ctx.fillStyle =
                                        isDarkMode() ?
                                        '#f9fafb' :
                                        '#374151';
                                    const total = chart.config
                                        .data.datasets[0].data
                                        .reduce((a, b) => a + b,
                                            0);
                                    const text = total;
                                    const textX = Math.round((
                                        width - ctx
                                        .measureText(
                                            text).width
                                    ) / 2);
                                    const textY = height / 2;
                                    ctx.fillText(text, textX,
                                        textY);
                                    ctx.save();
                                }
                            }]
                        });
                        pieCharts.push(pieChart);
                    });
                });
            },

            get globalStats() {
                const data = this.getFilteredData();
                const stats = {};
                Object.keys(this.statusMap).forEach(k => stats[k] = 0);
                data.forEach(item => {
                    Object.keys(this.statusMap).forEach(s => {
                        if (stats[s] !== undefined) stats[s] += (item.stats[
                            s] || 0);
                    });
                });
                return stats;
            }
        };
    });

Alpine.data("attendanceViewer",(data, initialMonth)=>{
    return {
        calendar: data,
        selectedMonth: initialMonth,
        selectedWeek: null,

        init() {
            this.setFirstWeek();
        },

        selectMonth(key) {
            this.selectedMonth = key;
            this.setFirstWeek();
        },

        setFirstWeek() {
            if (this.calendar[this.selectedMonth] && this.calendar[this.selectedMonth].weeks) {
                this.selectedWeek = Object.keys(this.calendar[this.selectedMonth].weeks)[0];
            }
        },

        // 1. ألوان الحالة النهائية لليوم
        getBadgeClass(color) {
            const map = {
                'green': 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
                'red': 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
                'rose': 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800', // غياب جزئي
                'yellow': 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
                'amber': 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800', // استئذان جزئي
                'orange': 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-800',
                'blue': 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
                'gray': 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-400',
            };
            return map[color] || map['gray'];
        },

        // 2. أيقونة الحالة النهائية
        getStatusIcon(color) {
            const map = {
                'green': 'fas fa-check-circle',
                'red': 'fas fa-times-circle',
                'rose': 'fas fa-adjust', // نصف دائرة للجزئي
                'yellow': 'fas fa-file-medical',
                'amber': 'fas fa-file-alt',
                'orange': 'fas fa-history',
                'blue': 'fas fa-umbrella-beach',
                'gray': 'fas fa-minus-circle',
            };
            return map[color] || 'fas fa-circle';
        },

        // 3. ألوان النصوص للحصص الفردية
        getSlotTextColor(status) {
            // Enum: 1=Present, 2=Absent, 3=Late, 4=Excused
            if (status == 1) return 'text-green-600 dark:text-green-400';
            if (status == 2) return 'text-red-600 dark:text-red-400';
            if (status == 3) return 'text-orange-600 dark:text-orange-400';
            if (status == 4) return 'text-yellow-600 dark:text-yellow-400';
            return 'text-gray-400';
        },

        // 4. أيقونات الحصص الفردية
        getSlotIcon(status) {
            if (status == 1) return 'fas fa-check';
            if (status == 2) return 'fas fa-times';
            if (status == 3) return 'fas fa-clock';
            if (status == 4) return 'fas fa-paperclip';
            return 'fas fa-question';
        }
    }
})
});
