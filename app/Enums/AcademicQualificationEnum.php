<?php

namespace App\Enums;

enum AcademicQualificationEnum: string
{
    case Diploma = 'diploma';
    case HigherDiploma = 'higher_diploma';
    case Bachelor = 'bachelor';
    case GraduateDiploma = 'graduate_diploma';
    case Master = 'master';
    case ProfessionalMaster = 'professional_master';
    case PhD = 'phd';
    case PostDoctorate = 'post_doctorate';

    public function label(): string
    {
        return match ($this) {
            self::Diploma => 'دبلوم',
            self::HigherDiploma => 'دبلوم عالي',
            self::Bachelor => 'بكالوريوس',
            self::GraduateDiploma => 'دبلوم دراسات عليا',
            self::Master => 'ماجستير',
            self::ProfessionalMaster => 'ماجستير مهني',
            self::PhD => 'دكتوراه',
            self::PostDoctorate => 'ما بعد الدكتوراه',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }
}
