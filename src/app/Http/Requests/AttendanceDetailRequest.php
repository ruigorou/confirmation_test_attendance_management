<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'remarks' => 'required|string|min:1',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'remarks.required' => '備考欄を記入してください。',
            'remarks.min' => '備考欄を記入してください。',
            'clock_in.required' => '出勤時間は必須です。',
            'clock_in.date_format' => '出勤時間の形式が正しくありません。',
            'clock_out.required' => '退勤時間は必須です。',
            'clock_out.date_format' => '退勤時間の形式が正しくありません。',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 出勤時間が退勤時間より後の場合にエラー
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            if ($clockIn && $clockOut && strtotime($clockIn) >= strtotime($clockOut)) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です。');
            }

            // 休憩時間のバリデーション
            $breakTimes = $this->request->all();
            foreach ($breakTimes as $key => $value) {
                // break_start_* と break_end_* をチェック
                if (preg_match('/^start_time(\d+)$/', $key, $matches)) {
                    $breakId = $matches[1];
                    $breakStart = $value;
                    $breakEnd = $this->input("end_time{$breakId}");

                    if ($breakStart && $breakEnd) {
                        // 休憩時間が出勤時間より前の場合
                        if (strtotime($breakStart) < strtotime($clockIn)) {
                            $validator->errors()->add("start_time{$breakId}", '休憩時間が不適切な値です。');
                        }

                        // 休憩開始時間が終了時間より後の場合
                        if (strtotime($breakStart) >= strtotime($clockOut)) {
                            $validator->errors()->add("start_time{$breakId}", '休憩時間が不適切な値です。');
                        }

                        // 休憩時間が退勤時間より後の場合
                        if (strtotime($breakEnd) > strtotime($clockOut)) {
                            $validator->errors()->add("end_time{$breakId}", '休憩時間もしくは退勤時間が不適切な値です。');
                        }
                    }
                }
            }
        });
    }
}
