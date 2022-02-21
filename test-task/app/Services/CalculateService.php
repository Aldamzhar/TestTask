<?php

namespace App\Services;

use Illuminate\Http\Request;

class CalculateService{

    private int $monthly_calculated_indicator = 3063;
    private int $minimum_salary = 60000;
    private int $salary = 0;
    private int $mandatory_pension_fee = 0;
    private int $mandatory_social_medical_care = 0;
    private int $fees_for_mandatory_social_medical_care = 0;
    private int $social_payment = 0;
    private int $individual_income_tax = 0;




    public function calculate(Request $request) {
        $this->salary = $request->get('salary');
        $default_days_count = $request->get('default_days_count');
        $worked_days_count = $request->get('worked_days_count');
        $has_tax_pay = $request->get('has_tax_pay');
        $day = $request->get('day');
        $month = $request->get('month');
        $is_retiree = $request->get('is_retiree');
        $is_handicapped = $request->get('is_handicapped');

        $this->mandatory_pension_fee = $this->salary * 0.1;
        $this->mandatory_social_medical_care = $this->salary * 0.02;
        $this->fees_for_mandatory_social_medical_care = $this->salary * 0.02;
        $this->social_payment = ($this->salary - $this->mandatory_pension_fee) * 0.035;

        $final_salary = 0;

        if ($this->salary < 25 * $this->monthly_calculated_indicator) {
            if ($has_tax_pay) {
                $correction = ($this->salary - $this->mandatory_pension_fee - $this->minimum_salary - $this->fees_for_mandatory_social_medical_care) * 0.9;
                $this->individual_income_tax = ($this->salary - $this->mandatory_pension_fee - $this->minimum_salary - $this->fees_for_mandatory_social_medical_care - $correction);
            } else {
                $correction = ($this->salary - $this->mandatory_pension_fee - $this->fees_for_mandatory_social_medical_care) * 0.9;
                $this->individual_income_tax = ($this->salary - $this->mandatory_pension_fee - $this->fees_for_mandatory_social_medical_care - $correction);
            }
        }

        if ($is_retiree && $is_handicapped) {
            $final_salary = $this->salary;
        } else if ($is_retiree && !$is_handicapped) {
            $final_salary = $this->salary - $this->individual_income_tax;
        } else if (!$is_retiree && $is_handicapped) {
            $group_number = $request->get('handicapped_group');
            if ($this->salary > 882 * $this->monthly_calculated_indicator) {
                $this->salary = $this->salary - $this->individual_income_tax;
            }
            if ($group_number == 1 || $group_number == 2) {
                $final_salary = $this->salary - $this->social_payment;
            } else if ($group_number == 3) {
                $final_salary = $this->salary - $this->mandatory_pension_fee - $this->social_payment;
            }
        }
        return $final_salary;
    }

    public function saveCalculation(Request $request) {
        return response()->json([
            'individual_income_tax' => $this->individual_income_tax,
            'mandatory_pension_fee' => $this->mandatory_pension_fee,
            'mandatory_social_medical_care' => $this->mandatory_social_medical_care,
            'fees_for_mandatory_social_medical_care' => $this->fees_for_mandatory_social_medical_care,
            'social_payment' => $this->social_payment,
            'salary' => $this->salary,
            'final_salary' => $this->calculate($request)
        ]);
    }
}
