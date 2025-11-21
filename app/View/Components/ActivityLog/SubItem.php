<?php

namespace App\View\Components\ActivityLog;

use App\Enums\ActivityEventEnum;
use App\Models\Activity;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\Component;

class SubItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Activity $activity, public bool $isLast = false)
    {
        //
    }

    public function color()
    {
        return ActivityEventEnum::tryFrom($this->activity->event)?->color()
            ?? 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-700/50 dark:text-gray-400 dark:border-gray-600';
    }

    public function icon()
    {
        return ActivityEventEnum::tryFrom($this->activity->event)?->icon()
            ?? 'fas fa-history';
    }

    public function description()
    {
        if (Lang::has("activity_log.descriptions.{$this->activity->description}")) {
            if ($this->activity->subject_type) {
                $SubjectClass = $this->activity->subject_type;

                return __("activity_log.descriptions.{$this->activity->description}", [
                    'subject' => $SubjectClass::label(),
                ]);
            }

            return __("activity_log.descriptions.{$this->activity->description}");
        }

        return $this->activity->description;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.activity-log.sub-item');
    }
}
