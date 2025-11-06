<?php

namespace App\Traits;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait HandlesSafeDelete
{

    public function safeDelete(
        Model $model,
        string $redirectRoute,
        string $successMessage = 'تم الحذف بنجاح',
        ?string $errorMsg = null
    ) {
        try {
            $model->delete();

            return redirect()->to($redirectRoute)->with('success', $successMessage);

        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {

                $errorMsg = $errorMsg ?: 'عذراً، لا يمكن حذف هذا السجل لأنه مرتبط ببيانات أخرى في النظام.';

                return back()->with(['error' => $errorMsg]);
            }

            Log::error($e->getMessage());
            return back()->with(['error' => 'حدث خطأ غير متوقع أثناء الحذف.']);
        }
    }
}
