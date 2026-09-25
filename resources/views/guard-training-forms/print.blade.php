<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IN-1144 — {{ $form->form_number }}</title>
    <style>
        :root { --ink: #111; --line: #222; --muted: #555; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #e8e8e8; color: var(--ink); font-family: "Times New Roman", Times, serif; }
        .toolbar {
            position: sticky; top: 0; z-index: 20; display: flex; gap: .75rem; flex-wrap: wrap;
            align-items: center; justify-content: space-between; padding: .75rem 1rem;
            background: #1a2332; color: #fff; font-family: system-ui, sans-serif;
        }
        .toolbar button, .toolbar a {
            display: inline-flex; align-items: center; gap: .4rem; border: 0; border-radius: .4rem;
            padding: .5rem .9rem; font-size: .875rem; font-weight: 600; cursor: pointer; text-decoration: none;
        }
        .btn-print { background: #3AA62C; color: #fff; }
        .btn-back { background: #fff; color: #111; }
        .sheet {
            width: 8.5in; min-height: 11in; margin: 1rem auto; padding: .55in .6in;
            background: #fff; box-shadow: 0 8px 24px rgba(0,0,0,.15);
        }
        h1 { margin: 0; text-align: center; font-size: 15pt; letter-spacing: .02em; text-transform: uppercase; }
        .authority { text-align: center; font-size: 9pt; margin: .2rem 0 .55rem; }
        .meta { display: flex; justify-content: space-between; font-size: 9pt; margin-bottom: .4rem; color: var(--muted); }
        .section { border: 1.5px solid var(--line); margin-top: .45rem; }
        .section-title {
            background: #f3f3f3; border-bottom: 1px solid var(--line);
            padding: .2rem .4rem; font-size: 10pt; font-weight: 700; text-transform: uppercase;
        }
        .section-body { padding: .35rem .45rem; font-size: 10.5pt; }
        .row { display: flex; flex-wrap: wrap; gap: .35rem .75rem; margin-bottom: .3rem; }
        .field { flex: 1 1 140px; }
        .label { font-size: 8.5pt; color: var(--muted); text-transform: uppercase; }
        .value { border-bottom: 1px solid #999; min-height: 1.15rem; padding: .05rem 0; }
        .checks { display: grid; grid-template-columns: 1fr 1fr; gap: .25rem .6rem; }
        .check { display: flex; align-items: center; gap: .35rem; font-size: 10pt; }
        .box {
            width: 13px; height: 13px; border: 1.5px solid #222; display: inline-flex;
            align-items: center; justify-content: center; font-size: 11px; line-height: 1;
            font-weight: 700; color: #111; flex-shrink: 0;
        }
        table.lines { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table.lines th, table.lines td { border: 1px solid #888; padding: .22rem .3rem; text-align: left; }
        table.lines th { background: #f7f7f7; font-size: 8.5pt; text-transform: uppercase; }
        .footer-note { margin-top: .5rem; font-size: 8pt; color: var(--muted); text-align: center; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet { margin: 0; box-shadow: none; width: auto; min-height: auto; }
        }
    </style>
</head>
<body>
@php
    $mark = fn (bool $on) => $on ? '✓' : '';
    $weapons = $form->weaponsList();
@endphp

<div class="toolbar no-print">
    <div>
        <strong>Form IN-1144</strong>
        <span style="opacity:.8;margin-left:.5rem;">{{ $form->form_number }}</span>
    </div>
    <div style="display:flex;gap:.5rem;">
        <button type="button" class="btn-print" onclick="window.print()">Print / Save as PDF</button>
        @if(! empty($forAdmin))
            <a href="{{ route('admin.guard-training-forms.edit', $form) }}" class="btn-back">Back to edit</a>
        @else
            <a href="{{ route('student.certificates.index') }}" class="btn-back">Back to certificates</a>
        @endif
    </div>
</div>

<div class="sheet">
    <div class="meta">
        <span>FORM # IN-1144 (Rev 04/2023)</span>
        <span>{{ $form->form_number }}</span>
    </div>
    <h1>Certificate of Successful Completion of Guard Training</h1>
    <p class="authority">Authority: T.C.A. § 62-35-118, Administrative Rule 0780-05-02-.15</p>

    <div class="section">
        <div class="section-title">Registration Type</div>
        <div class="section-body checks">
            @foreach(\App\Models\GuardTrainingForm::REGISTRATION_TYPES as $value => $label)
                <div class="check"><span class="box">{{ $mark($form->registration_type === $value) }}</span> {{ $label }}</div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">Applicant Information</div>
        <div class="section-body">
            <div class="row">
                <div class="field"><div class="label">Last Name</div><div class="value">{{ $form->last_name }}</div></div>
                <div class="field"><div class="label">First Name</div><div class="value">{{ $form->first_name }}</div></div>
                <div class="field" style="flex:0 0 90px;"><div class="label">M.I.</div><div class="value">{{ $form->middle_initial }}</div></div>
            </div>
            <div class="row">
                <div class="field"><div class="label">Social Security Number</div><div class="value">{{ $form->formattedSsn() }}</div></div>
                <div class="field"><div class="label">Registration #</div><div class="value">{{ $form->registration_number }}</div></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Initial Training</div>
        <div class="section-body">
            <table class="lines">
                <thead>
                    <tr>
                        <th style="width:28px;"></th>
                        <th>Training</th>
                        <th style="width:110px;">Date</th>
                        <th style="width:80px;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="box">{{ $mark((bool) $form->initial_general) }}</span></td>
                        <td>Initial – Four (4) Hours General Guard Training</td>
                        <td>{{ optional($form->initial_general_date)->format('m/d/Y') }}</td>
                        <td>{{ $form->initial_general_score !== null ? $form->initial_general_score.'%' : '' }}</td>
                    </tr>
                    <tr>
                        <td><span class="box">{{ $mark((bool) $form->initial_firearms) }}</span></td>
                        <td>Initial – Eight (8) Hours Classroom Firearms Training</td>
                        <td>{{ optional($form->initial_firearms_date)->format('m/d/Y') }}</td>
                        <td>{{ $form->initial_firearms_score !== null ? $form->initial_firearms_score.'%' : '' }}</td>
                    </tr>
                    <tr>
                        <td><span class="box">{{ $mark((bool) $form->initial_marksmanship) }}</span></td>
                        <td>Initial – Four (4) Hours Marksmanship Training</td>
                        <td>{{ optional($form->initial_marksmanship_date)->format('m/d/Y') }}</td>
                        <td>{{ $form->initial_marksmanship_score !== null ? $form->initial_marksmanship_score.'%' : '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Weapon Information</div>
        <div class="section-body">
            <table class="lines">
                <thead>
                    <tr>
                        <th>Make / Model</th>
                        <th style="width:100px;">Caliber</th>
                        <th style="width:110px;">Date</th>
                        <th style="width:80px;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weapons as $weapon)
                        <tr>
                            <td>{{ $weapon['make_model'] }}</td>
                            <td>{{ $weapon['caliber'] }}</td>
                            <td>{{ filled($weapon['date'] ?? null) ? \Illuminate\Support\Carbon::parse($weapon['date'])->format('m/d/Y') : '' }}</td>
                            <td>{{ $weapon['score'] !== null ? $weapon['score'].'%' : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Renewals</div>
        <div class="section-body">
            <table class="lines">
                <thead>
                    <tr><th style="width:28px;"></th><th>Type</th><th style="width:110px;">Date</th><th style="width:80px;">Score</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="box">{{ $mark((bool) $form->classroom_renewal) }}</span></td>
                        <td>Classroom Renewal</td>
                        <td>{{ optional($form->classroom_renewal_date)->format('m/d/Y') }}</td>
                        <td>{{ $form->classroom_renewal_score !== null ? $form->classroom_renewal_score.'%' : '' }}</td>
                    </tr>
                    <tr>
                        <td><span class="box">{{ $mark((bool) $form->range_renewal) }}</span></td>
                        <td>Firing Range Renewal</td>
                        <td>{{ optional($form->range_renewal_date)->format('m/d/Y') }}</td>
                        <td>{{ $form->range_renewal_score !== null ? $form->range_renewal_score.'%' : '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Additional Classifications</div>
        <div class="section-body checks">
            @foreach([
                ['classification_cpr', 'CPR', 'classification_cpr_date'],
                ['classification_first_aid', 'First Aid', 'classification_first_aid_date'],
                ['classification_active_shooter', 'Active Shooter', 'classification_active_shooter_date'],
                ['classification_de_escalation', 'De-escalation', 'classification_de_escalation_date'],
                ['classification_safe_restraint', 'Safe Restraint', 'classification_safe_restraint_date'],
            ] as [$flag, $label, $dateField])
                <div class="check">
                    <span class="box">{{ $mark((bool) $form->{$flag}) }}</span>
                    {{ $label }}
                    @if($form->{$dateField})
                        <span style="color:#666;font-size:9pt;">({{ $form->{$dateField}->format('m/d/Y') }})</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">Trainer Information</div>
        <div class="section-body">
            <div class="row">
                <div class="field"><div class="label">Trainer Name</div><div class="value">{{ $form->trainer_name }}</div></div>
                <div class="field"><div class="label">Certification #</div><div class="value">{{ $form->trainer_certification_number }}</div></div>
            </div>
            <div class="row">
                <div class="field"><div class="label">Email</div><div class="value">{{ $form->trainer_email }}</div></div>
                <div class="field"><div class="label">Telephone</div><div class="value">{{ $form->trainer_phone }}</div></div>
            </div>
            <div class="row">
                <div class="field"><div class="label">Assistant Trainer</div><div class="value">{{ $form->assistant_trainer_name }}</div></div>
                <div class="field"><div class="label">Assistant Certification #</div><div class="value">{{ $form->assistant_trainer_certification_number }}</div></div>
            </div>
            <div class="row">
                <div class="field"><div class="label">Comments</div><div class="value" style="min-height:2.2rem;">{{ $form->comments }}</div></div>
            </div>
            <div class="row" style="margin-top:.6rem;">
                <div class="field"><div class="label">Trainer Signature</div><div class="value" style="min-height:1.6rem;"></div></div>
                <div class="field" style="flex:0 0 140px;"><div class="label">Date</div><div class="value">{{ optional($form->published_at ?? $form->updated_at)->format('m/d/Y') }}</div></div>
            </div>
        </div>
    </div>

    <p class="footer-note">
        Generated for TN Veterans Security · {{ $form->service?->title }}
        @if($form->isPublished()) · Issued to student {{ optional($form->published_at)->format('M j, Y') }} @endif
    </p>
</div>
</body>
</html>
