@extends('layouts.web.master')

@section('content')
    <main class="overflow-x-hidden">
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-3 sm:px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px]">
                    <h1
                        class="inner-hero-title text-[clamp(1.1rem,3.2vw,2.75rem)] md:text-[clamp(1.5rem,3.5vw,3.25rem)] leading-[1.1] mb-4 sm:mb-6"
                        data-aos="fade-down"
                        data-aos-duration="1000"
                    >
                        <span class="block">RELEASE OF LIABILITY,</span>
                        <span class="block">WAIVER OF CLAIMS,</span>
                        <span class="block">
                            AND <span class="text-(--primary-color)">ASSUMPTION OF RISK</span>
                        </span>
                        <span class="block text-[clamp(0.75rem,2vw,1.1rem)] md:text-lg mt-3 font-semibold tracking-wide normal-case">
                            Agreement — Instructional Training Program
                        </span>
                    </h1>
                    <p
                        class="text-white/90 text-[15px] sm:text-lg max-w-2xl leading-relaxed break-words"
                        data-aos="fade-up"
                        data-aos-delay="150"
                    >
                        Tn Veterans Security Services and Training L.L.C., d/b/a The Security Firm L.L.C.
                    </p>
                </div>
            </div>
        </section>

        <section class="py-10 sm:py-14 md:py-16 bg-white">
            <div class="container mx-auto w-full px-4 sm:px-6 lg:px-10">
                <article
                    class="legal-privacy-act text-[15px] sm:text-base text-[#333] leading-[1.65] break-words [&_a]:text-[var(--primary-color)] [&_a]:underline-offset-2 hover:[&_a]:underline"
                >
                    <header class="mb-8 sm:mb-10 pb-6 border-b border-gray-200">
                        <p class="font-semibold text-[var(--text-color)] text-base sm:text-lg mb-2">
                            Tn Veterans Security Services and Training L.L.C., d/b/a The Security Firm L.L.C.
                        </p>
                        <p class="mb-4">4400 Belmont Park Terrace, Suite 100, Nashville, TN 37215</p>
                        <div
                            class="rounded-md border border-amber-200 bg-amber-50 p-4 sm:p-5 text-sm sm:text-[15px] text-[#333] leading-relaxed"
                            role="note"
                        >
                            <strong class="text-[var(--text-color)] uppercase tracking-wide">Important — please read carefully before signing.</strong>
                            This is a legal document that affects your legal rights. By signing this document, you waive certain
                            legal rights, including the right to sue or claim compensation following an accident or injury. Read
                            this agreement in its entirety before signing.
                        </div>
                    </header>

                    <p class="mb-8 sm:mb-10">
                        In consideration of being permitted to participate in any instructional training program, course,
                        workshop, seminar, class, or activity (collectively, the &quot;Training Program&quot;) offered by Tn
                        Veterans Security Services and Training L.L.C., d/b/a The Security Firm L.L.C. (the &quot;Company&quot;),
                        the undersigned Participant agrees as follows:
                    </p>

                    <h2 class="legal-section-heading">1. Acknowledgment and Assumption of Risk</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="1.1">
                            <p class="mb-2">
                                The Participant acknowledges that participation in the Training Program involves inherent risks,
                                dangers, and hazards that may result in serious injury, illness, or death. These risks include, but
                                are not limited to:
                            </p>
                            <ul class="list-disc pl-5 space-y-1.5 marker:text-[var(--text-color)]">
                                <li>Physical injury, including but not limited to bruises, sprains, fractures, dislocations, concussions, and other bodily harm;</li>
                                <li>Property damage to personal belongings, vehicles, or equipment;</li>
                                <li>Emotional or psychological distress;</li>
                                <li>Exposure to communicable illness or disease;</li>
                                <li>Equipment malfunction, failure, or misuse;</li>
                                <li>Inadequate instruction, supervision, or training;</li>
                                <li>Actions, omissions, or negligence of other participants, instructors, or third parties;</li>
                                <li>Slips, trips, and falls on or near the training premises;</li>
                                <li>Muscular strains, sprains, overexertion, and repetitive motion injuries;</li>
                                <li>Environmental conditions, including weather, temperature, and terrain hazards; and</li>
                                <li>Other injuries, damages, or losses not specifically identified herein.</li>
                            </ul>
                        </x-legal-clause>
                        <x-legal-clause n="1.2">
                            The Participant understands that these risks may result in personal injury, illness, death, or damage
                            to personal property. The Participant voluntarily assumes full responsibility for all risks of loss,
                            injury, or damage—both known and unknown, foreseeable and unforeseeable—arising from or related to
                            participation in the Training Program.
                        </x-legal-clause>
                        <x-legal-clause n="1.3">
                            The Participant acknowledges that this assumption of risk is made freely and voluntarily, with full
                            knowledge and understanding of the nature, scope, and extent of the risks involved.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">2. Description of Training Activities</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="2.1">
                            This Agreement applies to all training programs, courses, classes, workshops, seminars, certifications,
                            continuing education, and activities offered by the Company, whether currently offered or developed in
                            the future, without limitation, including all instructional curricula and specialized training conducted
                            by any instructor, trainer, or agent of the Company.
                        </x-legal-clause>
                        <x-legal-clause n="2.2">
                            <strong>Equipment, Tools, and Materials.</strong> This Agreement covers the use of any and all
                            equipment, tools, firearms, protective gear, training aids, machinery, materials, and other items
                            used, provided, or required in connection with the Training Program, whether owned by the Company, the
                            Participant, or a third party.
                        </x-legal-clause>
                        <x-legal-clause n="2.3">
                            <strong>Physical Activities and Requirements.</strong> The Participant acknowledges that the Training
                            Program may involve physical demands including, but not limited to, standing, walking, running, lifting,
                            bending, use of physical force, weapons handling, defensive tactics, and other strenuous or potentially
                            hazardous physical activities. The Participant assumes all risks associated with such physical demands.
                        </x-legal-clause>
                        <x-legal-clause n="2.4">
                            <strong>Training Locations.</strong> This Agreement applies to training conducted at any and all
                            locations where the Company provides instruction, including but not limited to the Company&apos;s
                            primary facility at 4400 Belmont Park Terrace, Suite 100, Nashville, TN 37215, and training locations in
                            Robertson County, Davidson County, Sumner County, and Rutherford County, Tennessee, as well as any
                            other off-site, remote, mobile, temporary, or third-party locations used by the Company for training
                            purposes, whether currently in use or established in the future. The Participant assumes all risks
                            associated with travel to, from, and between all training locations.
                        </x-legal-clause>
                        <x-legal-clause n="2.5">
                            The Participant acknowledges that the Company may modify the activities, schedule, or location of the
                            Training Program as necessary, and the Participant assumes all risks associated with any such
                            modifications.
                        </x-legal-clause>
                        <x-legal-clause n="2.6">
                            <strong>Specific Course/Class Enrolled (if applicable):</strong>
                            <span class="mt-2 block border-b border-dashed border-gray-400 min-h-[1.75rem] w-full max-w-xl"></span>
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">3. Voluntary Participation and Right to Withdraw</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="3.1">
                            The Participant acknowledges that participation in the Training Program is entirely voluntary.
                        </x-legal-clause>
                        <x-legal-clause n="3.2">
                            The Company reserves the right, in its sole and absolute discretion, to refuse, restrict, or terminate
                            any Participant&apos;s involvement in the Training Program at any time, for any reason, with or without
                            cause, and without liability or obligation to refund any fees paid.
                        </x-legal-clause>
                        <x-legal-clause n="3.3">
                            The Participant may withdraw from the Training Program at any time; however, such withdrawal shall not
                            release the Participant from any obligations under this Agreement, including indemnification and
                            covenant not to sue provisions.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">4. Code of Conduct and Safety Compliance</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="4.1">
                            The Participant agrees to comply with all safety rules, regulations, policies, procedures, and
                            instructions issued by the Company, its instructors, staff, and agents at all times during participation
                            in the Training Program.
                        </x-legal-clause>
                        <x-legal-clause n="4.2">
                            Failure to follow safety rules or instructor directions, or engaging in reckless, negligent, disruptive,
                            or dangerous behavior, shall constitute grounds for immediate removal from the Training Program without
                            refund, warning, or prior notice.
                        </x-legal-clause>
                        <x-legal-clause n="4.3">
                            The Participant acknowledges that safety compliance is a condition of participation and that the
                            Company shall bear no liability for injuries or damages resulting from the Participant&apos;s failure to
                            follow safety rules or instructions.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">5. Release and Waiver of Liability</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="5.1">
                            In consideration of being permitted to participate in the Training Program, the Participant hereby
                            releases, waives, discharges, and covenants not to sue the Company, its owners, members, managers,
                            partners, officers, directors, employees, staff, agents, instructors, trainers, volunteers, independent
                            contractors, subcontractors, affiliates, subsidiaries, parent companies, sponsors, landlords, property
                            owners, facility operators, equipment suppliers, insurers, successors, and assigns (collectively, the
                            &quot;Released Parties&quot;) from any and all liability, claims, demands, actions, or causes of action
                            whatsoever, arising out of or related to any loss, damage, or injury—including death—that may be
                            sustained by the Participant, or to any property belonging to the Participant, during or as a result of
                            participation in the Training Program.
                        </x-legal-clause>
                        <x-legal-clause n="5.2">
                            This release and waiver applies regardless of whether such loss, damage, or injury is caused by the
                            negligence, fault, or conduct of the Released Parties, or from any other cause whatsoever.
                        </x-legal-clause>
                        <x-legal-clause n="5.3">
                            <strong>Limitation:</strong> Notwithstanding the foregoing, this waiver and release shall not apply to
                            claims arising from the gross negligence or willful misconduct of the Released Parties.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">6. Indemnification and Hold Harmless</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="6.1">
                            The Participant agrees to indemnify, defend, and hold harmless the Released Parties from any and all
                            claims, damages, losses, liabilities, costs, and expenses (including reasonable attorney&apos;s fees and
                            court costs) arising out of or resulting from the Participant&apos;s participation in the Training Program,
                            including but not limited to any claim made by the Participant or any third party.
                        </x-legal-clause>
                        <x-legal-clause n="6.2">
                            This indemnification obligation shall include, without limitation, any claims brought by third parties as
                            a result of the Participant&apos;s actions, omissions, or conduct during or in connection with the Training
                            Program.
                        </x-legal-clause>
                        <x-legal-clause n="6.3">
                            The Participant&apos;s indemnification obligations extend to any and all legal proceedings, including but
                            not limited to lawsuits, arbitration proceedings, administrative hearings, regulatory investigations,
                            mediation, and any other form of dispute resolution, whether initiated by the Participant, a third party,
                            an attorney acting on the Participant&apos;s behalf, an insurance carrier, a government agency, or any other
                            entity.
                        </x-legal-clause>
                        <x-legal-clause n="6.4">
                            The Participant agrees that the indemnification obligations set forth herein shall survive the
                            termination or expiration of this Agreement and shall remain in full force and effect regardless of any
                            claim that this Agreement or any provision hereof is void, voidable, or unenforceable.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">7. Mandatory Binding Arbitration</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="7.1">
                            Any dispute, claim, or controversy arising out of or relating to this Agreement, the Participant&apos;s
                            participation in the Training Program, or any alleged breach of this Agreement, shall be resolved
                            exclusively through final and binding arbitration administered in accordance with the rules of the
                            American Arbitration Association (AAA).
                        </x-legal-clause>
                        <x-legal-clause n="7.2">
                            Arbitration shall take place in Robertson County, Davidson County, Sumner County, or Rutherford County,
                            Tennessee, as designated by the Company in its sole discretion, or such other location in the State of
                            Tennessee as the Company may designate. The arbitration shall be conducted by a single arbitrator
                            selected in accordance with AAA rules.
                        </x-legal-clause>
                        <x-legal-clause n="7.3">
                            The arbitrator&apos;s decision shall be final and binding upon both parties and may be entered as a
                            judgment in any court of competent jurisdiction.
                        </x-legal-clause>
                        <x-legal-clause n="7.4">
                            The Participant agrees that any arbitration shall be conducted on an individual basis only. The
                            Participant hereby waives any right to participate in a class action, collective action, consolidated
                            action, or representative proceeding of any kind against the Company or any of the Released Parties.
                        </x-legal-clause>
                        <x-legal-clause n="7.5">
                            Each party shall bear its own costs and fees associated with the arbitration, including attorney&apos;s
                            fees, unless the arbitrator determines otherwise in the final award.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">8. Waiver of Right to Sue / Covenant Not to Sue</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="8.1">
                            The Participant agrees not to sue, file a lawsuit, or initiate any legal proceeding against the Company
                            or any of the Released Parties for any claims, losses, damages, or injuries arising from or related to
                            participation in the Training Program.
                        </x-legal-clause>
                        <x-legal-clause n="8.2">
                            If the Participant files a lawsuit or legal action in violation of this Agreement, the Participant shall
                            be solely responsible for all costs, fees, and expenses incurred by the Company and Released Parties in
                            defending such action, including but not limited to reasonable attorney&apos;s fees, court costs, filing
                            fees, service of process fees, expert witness fees, deposition costs, and all related litigation expenses.
                        </x-legal-clause>
                        <x-legal-clause n="8.3">
                            The Participant acknowledges that this covenant not to sue is a material inducement for the Company to
                            permit the Participant to participate in the Training Program, and that the Company has relied upon this
                            covenant in allowing such participation.
                        </x-legal-clause>
                        <x-legal-clause n="8.4">
                            This covenant not to sue extends to all forms of legal process, including but not limited to civil
                            lawsuits, administrative complaints, regulatory filings, demand letters sent by attorneys, insurance
                            subrogation claims, liens, garnishments, and any other legal or equitable proceeding in any forum.
                        </x-legal-clause>
                        <x-legal-clause n="8.5">
                            The Participant agrees that no attorney, law firm, insurance company, or any other third party may
                            bring a claim, demand, or action on the Participant&apos;s behalf against the Company or any Released Party,
                            whether by assignment, subrogation, or otherwise. The Participant shall not assign, transfer, or convey any
                            claim, cause of action, or right to sue arising under or related to this Agreement to any third party,
                            including but not limited to attorneys, law firms, collection agencies, or insurance carriers.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">9. Attorney&apos;s Fees, Court Costs, and Litigation Expenses</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="9.1">
                            In the event any legal action, arbitration proceeding, or other proceeding is brought by either party to
                            enforce or interpret any provision of this Agreement, the prevailing party shall be entitled to recover
                            from the non-prevailing party all reasonable attorney&apos;s fees, court costs, filing fees, arbitration fees,
                            expert witness fees, and other expenses incurred in connection with such action or proceeding.
                        </x-legal-clause>
                        <x-legal-clause n="9.2">
                            If the Participant initiates any legal claim, lawsuit, or proceeding against the Company or any Released
                            Party in breach of this Agreement and does not prevail, the Participant agrees to fully reimburse the
                            Company for all costs of defense, including but not limited to attorney&apos;s fees, paralegal fees, expert
                            fees, court costs, arbitration costs, and any other related expenses.
                        </x-legal-clause>
                        <x-legal-clause n="9.3">
                            The obligations set forth in this section shall survive the termination or expiration of this Agreement
                            and shall apply regardless of the forum in which any dispute is resolved.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">10. Statute of Limitations</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="10.1">
                            The Participant agrees that any claim or cause of action arising out of or relating to this Agreement or
                            the Participant&apos;s participation in the Training Program must be filed within one (1) year from the date
                            the claim or cause of action accrues, regardless of any longer statute of limitations that may be provided
                            by law.
                        </x-legal-clause>
                        <x-legal-clause n="10.2">
                            Failure to file a claim within this one (1) year period shall constitute a complete and absolute waiver and
                            bar of such claim.
                        </x-legal-clause>
                        <x-legal-clause n="10.3">
                            The Participant acknowledges that this shortened limitation period is reasonable and is a material term
                            of this Agreement.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">11. Waiver of Jury Trial</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="11.1">
                            <div class="rounded-md border border-gray-300 bg-[#f8f8f8] p-4 sm:p-5 text-xs sm:text-sm font-semibold uppercase tracking-wide text-[var(--text-color)] leading-relaxed">
                                To the fullest extent permitted by law, the Participant hereby knowingly, voluntarily, and irrevocably
                                waives any right to a trial by jury in any action, proceeding, or claim arising out of or relating to
                                this Agreement or the Participant&apos;s participation in the Training Program. The Participant
                                acknowledges that this waiver is a material inducement for the Company to permit participation in the
                                Training Program.
                            </div>
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">12. Waiver of Subrogation</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="12.1">
                            The Participant hereby waives any right of subrogation that the Participant&apos;s insurance carrier or any
                            other third party may have against the Company or any of the Released Parties. The Participant agrees to
                            notify their insurance carrier(s) of this waiver and to ensure that their insurance policies do not
                            impair or invalidate the protections afforded to the Released Parties under this Agreement.
                        </x-legal-clause>
                        <x-legal-clause n="12.2">
                            The Participant agrees that no insurance company, health care provider, government entity, or any other
                            third party shall have any right to recover from the Company or any Released Party any amounts paid on
                            behalf of the Participant for injuries, losses, or damages arising from the Participant&apos;s participation
                            in the Training Program.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">13. Medical Acknowledgment</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="13.1">
                            The Participant certifies that they are in good physical health, physically fit, and have no medical
                            condition, disability, or impairment that would prevent safe participation in the Training Program, or have
                            disclosed all such conditions in writing to the Company prior to participation.
                        </x-legal-clause>
                        <x-legal-clause n="13.2">
                            The Participant authorizes the Company and its personnel to seek and obtain emergency medical treatment on
                            the Participant&apos;s behalf in the event of injury, accident, or illness during the Training Program. The
                            Participant agrees to bear all costs associated with such emergency medical treatment, including ambulance
                            services, hospitalization, and physician fees.
                        </x-legal-clause>
                        <x-legal-clause n="13.3">
                            <p class="font-semibold text-[var(--text-color)] mb-2">Medical Conditions Disclosure</p>
                            <p class="mb-3 text-sm sm:text-base">
                                Please list any known medical conditions, allergies, or physical limitations below:
                            </p>
                            <p class="mb-3 text-sm text-[#555] italic">
                                List any known medical conditions, allergies, or physical limitations, or write &quot;None&quot;.
                            </p>
                            <div class="space-y-2 mb-4">
                                <div class="border-b border-dashed border-gray-400 min-h-[1.5rem]"></div>
                                <div class="border-b border-dashed border-gray-400 min-h-[1.5rem]"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm font-semibold text-[var(--text-color)]">Emergency Contact Name</span>
                                    <div class="border-b border-dashed border-gray-400 min-h-[1.75rem] mt-1"></div>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-[var(--text-color)]">Phone</span>
                                    <div class="border-b border-dashed border-gray-400 min-h-[1.75rem] mt-1"></div>
                                </div>
                            </div>
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">14. Refund and Cancellation Policy</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="14.1">
                            All fees paid for the Training Program are non-refundable except as expressly provided in writing by the
                            Company.
                        </x-legal-clause>
                        <x-legal-clause n="14.2">
                            In the event the Participant is removed from the Training Program for failure to comply with the Code of
                            Conduct, safety violations, or at the Company&apos;s discretion, no refund shall be issued.
                        </x-legal-clause>
                        <x-legal-clause n="14.3">
                            Cancellations by the Participant must be submitted in writing. The Company reserves the right to establish
                            and modify cancellation deadlines, fees, and policies at its sole discretion.
                        </x-legal-clause>
                        <x-legal-clause n="14.4">
                            If the Company cancels or reschedules a Training Program, the Company may, at its sole discretion, offer a
                            credit toward a future program, reschedule the Participant, or issue a partial or full refund. The Company
                            shall have no further liability to the Participant beyond the amount of fees actually paid.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">15. Confidentiality and Intellectual Property</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="15.1">
                            The Participant acknowledges that all training materials, curricula, lesson plans, manuals, handouts,
                            presentations, videos, techniques, methods, processes, and proprietary information provided by the Company
                            in connection with the Training Program (collectively, &quot;Confidential Information&quot;) are the exclusive
                            property of the Company and are protected by applicable intellectual property laws.
                        </x-legal-clause>
                        <x-legal-clause n="15.2">
                            The Participant agrees not to copy, reproduce, distribute, share, publish, photograph, record,
                            reverse-engineer, or otherwise disseminate any Confidential Information, in whole or in part, without the
                            prior written consent of the Company.
                        </x-legal-clause>
                        <x-legal-clause n="15.3">
                            The Participant shall not use any Confidential Information for any purpose other than personal participation
                            in the Training Program.
                        </x-legal-clause>
                        <x-legal-clause n="15.4">
                            This confidentiality obligation shall survive the termination or expiration of this Agreement and shall
                            remain in effect indefinitely.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">16. Media Release (Optional)</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="16.1">
                            The Company may wish to photograph, video record, audio record, or otherwise capture the likeness of the
                            Participant during the Training Program for promotional, educational, marketing, or informational purposes.
                            By consenting below, the Participant grants the Company a perpetual, royalty-free, worldwide license to use
                            such materials without further compensation or notification to the Participant.
                        </x-legal-clause>
                        <x-legal-clause n="16.2">
                            <p class="font-semibold text-[var(--text-color)] mb-3">Please indicate your preference:</p>
                            <ul class="space-y-3 list-none pl-0">
                                <li class="flex gap-3 items-start">
                                    <span class="shrink-0 font-mono text-lg leading-none" aria-hidden="true">☐</span>
                                    <span>I <strong>consent</strong> to the use of my photograph, video, audio, or likeness for the purposes described above.</span>
                                </li>
                                <li class="flex gap-3 items-start">
                                    <span class="shrink-0 font-mono text-lg leading-none" aria-hidden="true">☐</span>
                                    <span>I <strong>do not consent</strong> to the use of my photograph, video, audio, or likeness for the purposes described above.</span>
                                </li>
                            </ul>
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">17. Communications Consent</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="17.1">
                            The Participant consents to receive communications from the Company, including but not limited to emails,
                            text messages, phone calls, and postal mail, regarding the Training Program, scheduling, safety updates,
                            policy changes, and other training-related matters.
                        </x-legal-clause>
                        <x-legal-clause n="17.2">
                            The Participant may opt out of non-essential marketing communications at any time by providing written
                            notice to the Company, but acknowledges that certain training-related and safety communications are
                            mandatory and cannot be opted out of.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">18. Force Majeure</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="18.1">
                            The Company shall not be liable for any failure to perform, delay in performance, cancellation, or
                            modification of the Training Program resulting from events beyond the Company&apos;s reasonable control,
                            including but not limited to acts of God, natural disasters, severe weather, fire, flood, earthquake,
                            pandemic, epidemic, public health emergency, government orders, civil unrest, terrorism, war, power outages,
                            utility failures, equipment failures not caused by the Company&apos;s negligence, or any other force majeure
                            event.
                        </x-legal-clause>
                        <x-legal-clause n="18.2">
                            In such events, the Company may, at its sole discretion, reschedule the Training Program, offer a credit,
                            or cancel the program without further liability to the Participant.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">19. Governing Law and Severability</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="19.1">
                            This Agreement shall be governed by and construed in accordance with the laws of the State of Tennessee,
                            without regard to its conflict of law provisions. Any dispute arising under or in connection with this Agreement
                            shall be subject to the exclusive jurisdiction of the courts located in Tennessee.
                        </x-legal-clause>
                        <x-legal-clause n="19.2">
                            If any provision of this Agreement is found to be invalid, illegal, or unenforceable by a court of competent
                            jurisdiction, such finding shall not affect the validity, legality, or enforceability of the remaining
                            provisions, which shall continue in full force and effect.
                        </x-legal-clause>
                        <x-legal-clause n="19.3">
                            This Agreement shall be binding upon the Participant and the Participant&apos;s heirs, executors, administrators,
                            personal representatives, and assigns.
                        </x-legal-clause>
                        <x-legal-clause n="19.4">
                            <strong>No Reliance on Representations.</strong> The Participant acknowledges that neither the Company nor
                            any Released Party has made any representations, warranties, promises, or guarantees regarding the safety of
                            the Training Program, the qualifications of instructors, the condition of equipment or facilities, or the
                            outcome of participation. The Participant is not relying on any oral or written statement not contained in
                            this Agreement.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">20. Entire Agreement</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="20.1">
                            This document constitutes the entire agreement between the Participant and the Company with respect to the
                            subject matter herein and supersedes all prior or contemporaneous agreements, representations, warranties, and
                            understandings, whether written or oral.
                        </x-legal-clause>
                        <x-legal-clause n="20.2">
                            No amendment, modification, or waiver of any provision of this Agreement shall be effective unless made in
                            writing and signed by both the Participant and an authorized representative of the Company.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">21. Electronic Signatures</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="21.1">
                            The parties agree that this Agreement may be executed by electronic signature, which shall be considered as
                            an original signature for all purposes and shall have the same force and effect as an original signature on
                            paper.
                        </x-legal-clause>
                        <x-legal-clause n="21.2">
                            This Agreement and any electronically signed copy shall be deemed valid, binding, and enforceable to the
                            fullest extent permitted by law, including under the Tennessee Uniform Electronic Transactions Act (Tenn.
                            Code Ann. § 47-10-101 et seq.) and the federal Electronic Signatures in Global and National Commerce Act
                            (E-SIGN Act, 15 U.S.C. § 7001 et seq.).
                        </x-legal-clause>
                        <x-legal-clause n="21.3">
                            The Participant agrees that their electronic signature constitutes their intent to sign and be bound by this
                            Agreement.
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">22. Participant Acknowledgment</h2>
                    <div class="rounded-md border border-gray-300 bg-[#f8f8f8] p-4 sm:p-6 mb-8 sm:mb-10">
                        <p class="text-xs sm:text-sm font-semibold uppercase tracking-wide text-[var(--text-color)] leading-relaxed">
                            I have read this Release of Liability, Waiver of Claims, and Assumption of Risk Agreement. I fully
                            understand its terms and understand that I am giving up substantial rights, including my right to sue, my
                            right to a jury trial, and my right to participate in a class action. I agree to resolve any disputes through
                            binding arbitration. I acknowledge that I am signing this agreement freely and voluntarily, and intend my
                            signature to be a complete and unconditional release of all liability to the greatest extent allowed by law.
                        </p>
                    </div>

                    <div class="mt-10 sm:mt-12 space-y-8 sm:space-y-10">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-[var(--text-color)] uppercase tracking-wide mb-4">
                                Participant Signature
                            </h3>
                            <div class="space-y-4 rounded-lg border border-gray-300 bg-white p-4 sm:p-5">
                                <div class="grid grid-cols-1 gap-4 sm:gap-5">
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Participant&apos;s Printed Name</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Participant&apos;s Signature</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem] max-w-xs">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Date</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Email Address</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem] max-w-md">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Phone Number</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-[var(--text-color)] uppercase tracking-wide mb-2">
                                For Minors (if participant is under 18 years of age)
                            </h3>
                            <p class="text-sm text-[#555] mb-4">
                                If the Participant is a minor (under 18 years of age), a parent or legal guardian must read, understand,
                                and sign this Agreement on behalf of the minor Participant. The parent or legal guardian agrees to all
                                terms and conditions contained herein on behalf of the minor and accepts full responsibility.
                            </p>
                            <div class="space-y-4 rounded-lg border border-gray-300 bg-white p-4 sm:p-5">
                                <div class="grid grid-cols-1 gap-4 sm:gap-5">
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Parent/Guardian Printed Name</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Parent/Guardian Signature</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Relationship to Participant</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem] max-w-xs">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Date</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-[var(--text-color)] uppercase tracking-wide mb-4">
                                Company Representative (optional witness)
                            </h3>
                            <div class="space-y-4 rounded-lg border border-gray-300 bg-white p-4 sm:p-5">
                                <div class="grid grid-cols-1 gap-4 sm:gap-5">
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Representative Name</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Representative Signature</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem] max-w-xs">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Date</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h2 class="legal-section-heading">Contact Information</h2>
                    <p class="mb-4">
                        @if ($siteSettings && $siteSettings->email)
                            Email:
                            <a class="text-(--primary-color) hover:underline" href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a>
                            <br />
                        @endif
                        @if ($siteSettings && $siteSettings->phone)
                            Phone: {{ $siteSettings->phone }}<br />
                        @endif
                        @if ($siteSettings && $siteSettings->address)
                            Address: {{ $siteSettings->address }}
                        @endif
                    </p>

                    <footer class="mt-10 pt-6 border-t border-gray-200 text-xs sm:text-sm text-[#666] leading-relaxed space-y-2">
                        <p>
                            This document is a template and should be reviewed by a qualified legal professional before use. Laws
                            regarding liability waivers vary by jurisdiction.
                        </p>
                    </footer>
                </article>
            </div>
        </section>
    </main>
@endsection
