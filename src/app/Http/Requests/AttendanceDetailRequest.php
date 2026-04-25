<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceDetailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'remarks' => 'required|string|min:1',
            'new_start_time' => 'nullable|date_format:H:i',
            'new_end_time' => 'nullable|date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'remarks.required' => '備考欄を記入してください。',
            'remarks.min' => '備考欄を記入してください。',
            'clock_in.required' => '出勤時間は必須です。',
            'clock_in.date_format' => '出勤時間の形式が正しくありません。',
            'clock_out.required' => '退勤時間は必須です。',
            'clock_out.date_format' => '退勤時間の形式が正しくありません。',
            'new_start_time.date_format' => '休憩開始時間の形式が正しくありません。',
            'new_end_time.date_format' => '休憩終了時間の形式が正しくありません。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            if (!$clockIn || !$clockOut) {
                return;
            }

            // 出勤・退勤の順序チェック
            if (strtotime($clockIn) >= strtotime($clockOut)) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です。');
                return;
            }

            // 既存休憩のバリデーションと最終終了時刻の収集
            $lastBreakEnd = null;
            $allInput = $this->all();

            // IDを抽出してソート（登録順に検証）
            $breakIds = [];
            foreach (array_keys($allInput) as $key) {
                if (preg_match('/^start_time(\d+)$/', $key, $matches)) {
                    $breakIds[] = (int) $matches[1];
                }
            }
            sort($breakIds);

            foreach ($breakIds as $breakId) {
                $breakStart = $this->input("start_time{$breakId}");
                $breakEnd = $this->input("end_time{$breakId}");

                if (empty($breakStart) && empty($breakEnd)) {
                    continue;
                }

                if (!empty($breakStart) && !empty($breakEnd)) {
                    // 出勤時間より前
                    if (strtotime($breakStart) < strtotime($clockIn)) {
                        $validator->errors()->add("start_time{$breakId}", '');
                    }
                    // 前の休憩終了より前
                    if ($lastBreakEnd !== null && strtotime($breakStart) < strtotime($lastBreakEnd)) {
                        $validator->errors()->add("start_time{$breakId}", '休憩時間が不適切な値です。');
                    }
                    // 休憩開始≧休憩終了
                    if (strtotime($breakStart) >= strtotime($breakEnd)) {
                        $validator->errors()->add("start_time{$breakId}", '休憩時間が不適切な値です。');
                    }
                    // 退勤時間より後
                    if (strtotime($breakEnd) > strtotime($clockOut)) {
                        $validator->errors()->add("end_time{$breakId}", '休憩時間もしくは退勤時間が不適切な値です。');
                    }

                    $lastBreakEnd = $breakEnd;
                }
            }

            // 新規休憩のバリデーション（片方だけ入力されている場合もエラー）
            $newStart = $this->input('new_start_time');
            $newEnd = $this->input('new_end_time');

            if (empty($newStart) && empty($newEnd)) {
                return;
            }

            if (empty($newStart)) {
                $validator->errors()->add('new_start_time', '休憩開始時間を入力してください。');
                return;
            }
            if (empty($newEnd)) {
                $validator->errors()->add('new_end_time', '休憩終了時間を入力してください。');
                return;
            }

            // 出勤時間より前
            if (strtotime($newStart) < strtotime($clockIn)) {
                $validator->errors()->add('new_start_time', '休憩時間が不適切な値です。');
            }
            // 前の休憩終了より前
            if ($lastBreakEnd !== null && strtotime($newStart) < strtotime($lastBreakEnd)) {
                $validator->errors()->add('new_start_time', '休憩時間が不適切な値です。');
            }
            // 休憩開始≧休憩終了
            if (strtotime($newStart) >= strtotime($newEnd)) {
                $validator->errors()->add('new_start_time', '休憩時間が不適切な値です。');
            }
            // 退勤時間より後
            if (strtotime($newEnd) > strtotime($clockOut)) {
                $validator->errors()->add('new_end_time', '休憩時間もしくは退勤時間が不適切な値です。');
            }
        });
    }
}
