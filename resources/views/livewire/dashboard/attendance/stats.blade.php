<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-300">
                <div
                    class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
                    x-data="attendanceCharts(@js($this->chartData))"
                    x-init="init()"
                    x-cloak
                >
                    {{-- Header & Controls --}}
                    <div class="mb-8 space-y-4">
                        <div
                            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-100 dark:border-gray-700 pb-4">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                                نسبة الحضور الأسبوعية
                            </h2>

                            {{-- Chart Type Selector --}}
                            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                <button
                                    @click="setChartType('bar')"
                                    :class="{ 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400': chartType === 'bar', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': chartType !== 'bar' }"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center"
                                >
                                    <i class="fas fa-chart-bar me-2"></i>أعمدة
                                </button>
                                <button
                                    @click="setChartType('pie')"
                                    :class="{ 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400': chartType === 'pie', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': chartType !== 'pie' }"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center"
                                >
                                    <i class="fas fa-chart-pie me-2"></i>دائري
                                </button>
                                <button
                                    @click="setChartType('line')"
                                    :class="{ 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400': chartType === 'line', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200': chartType !== 'line' }"
                                    class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center"
                                >
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
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1"
                                >المرحلة
                                    الدراسية</label>
                                <select
                                    x-model="filters.stage_id"
                                    @change="handleFilterChange('stage')"
                                    class="w-full border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm appearance-none cursor-pointer"
                                >
                                    <option value="">جميع المراحل</option>
                                    <template
                                        x-for="stage in getStages()"
                                        :key="stage.id"
                                    >
                                        <option
                                            :value="stage.id"
                                            x-text="stage.name"
                                        ></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Grade Filter --}}
                            <div :class="{ 'opacity-50 pointer-events-none': !filters.stage_id }">
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1"
                                >الصف
                                    الدراسي</label>
                                <select
                                    x-model="filters.grade_id"
                                    @change="handleFilterChange('grade')"
                                    class="w-full border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm appearance-none cursor-pointer"
                                >
                                    <option value="">جميع الصفوف</option>
                                    <template
                                        x-for="grade in getGrades()"
                                        :key="grade.id"
                                    >
                                        <option
                                            :value="grade.id"
                                            x-text="grade.name"
                                        ></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Section Filter --}}
                            <div :class="{ 'opacity-50 pointer-events-none': !filters.grade_id }">
                                <label
                                    class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1"
                                >الشعبة</label>
                                <select
                                    x-model="filters.section_id"
                                    @change="handleFilterChange('section')"
                                    class="w-full border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm appearance-none cursor-pointer"
                                >
                                    <option value="">جميع الشعب</option>
                                    <template
                                        x-for="section in getSections()"
                                        :key="section.id"
                                    >
                                        <option
                                            :value="section.id"
                                            x-text="section.name"
                                        ></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Reset Button --}}
                            <div class="flex items-end">
                                <button
                                    @click="resetFilters()"
                                    class="w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                >
                                    <i class="fas fa-undo me-2"></i>إعادة تعيين
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Charts Area --}}
                    <div class="min-h-[400px] relative">
                        {{-- Standard View (Bar/Line) --}}
                        <div
                            x-show="chartType !== 'pie'"
                            class="w-full h-96 transition-all duration-300 p-2"
                        >
                            <canvas id="mainCanvas"></canvas>
                        </div>

                        {{-- Pie Grid View --}}
                        <div
                            x-show="chartType === 'pie'"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            <template
                                x-for="(item, index) in pieDataItems"
                                :key="index"
                            >
                                <div
                                    class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center hover:shadow-md transition-shadow">
                                    <h4
                                        class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-4 truncate w-full text-center"
                                        x-text="item.label"
                                    ></h4>
                                    <div class="relative w-48 h-48">
                                        <canvas :id="'pieCanvas_' + index"></canvas>
                                    </div>
                                </div>
                            </template>
                            {{-- Empty State for Pie --}}
                            <div
                                x-show="pieDataItems.length === 0"
                                class="col-span-full text-center text-gray-400 dark:text-gray-500 py-10"
                            >
                                لا توجد بيانات لعرضها
                            </div>
                        </div>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                        <template
                            x-for="statusKey in Object.keys(statusMap)"
                            :key="statusKey"
                        >
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-200 dark:border-gray-600 text-center transform transition duration-200 hover:scale-105 hover:shadow-md cursor-default"
                                x-show="globalStats[statusKey] > 0"
                            >
                                <div
                                    class="text-[10px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-widest mb-1"
                                    x-text="statusMap[statusKey].label"
                                ></div>
                                <div
                                    class="text-2xl font-black"
                                    :style="'color: ' + statusMap[statusKey].color"
                                    x-text="globalStats[statusKey]"
                                ></div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
