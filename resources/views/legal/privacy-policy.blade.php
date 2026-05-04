@extends('layouts.web.master')

@section('content')
    <main class="overflow-x-hidden">
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-3 sm:px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px]">
                    <h1
                        class="inner-hero-title text-[clamp(1.75rem,5.5vw,5rem)] md:text-[80px] leading-[1.05] mb-4 sm:mb-6"
                        data-aos="fade-down"
                        data-aos-duration="1000"
                    >
                        <span class="block sm:inline">PRIVACY ACT</span>
                        <span class="text-(--primary-color) block sm:inline sm:ms-2">STATEMENT</span>
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
                            Tn Veterans Security Services and Training L.L.C.,<br class="sm:hidden" />
                            <span class="hidden sm:inline"> </span>d/b/a The Security Firm L.L.C.
                        </p>
                        <p class="mb-1">4400 Belmont Park Terrace, Suite 100, Nashville, TN 37215</p>
                        <p class="text-sm sm:text-base text-[#555] mt-3">
                            <strong class="text-[var(--text-color)]">Effective Date:</strong>
                            The date on which this document is signed by the Participant.
                        </p>
                    </header>

                    <h2 class="legal-section-heading">Section 1: Purpose and Scope</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="1.1">
                        
                            The purpose of this Privacy Act Statement (“Statement”) is to inform participants, students,
                            employees, contractors, visitors, and website users (“Participants”) of Tn Veterans Security
                            Services and Training L.L.C., d/b/a The Security Firm L.L.C. (the “Company”), about the types of
                            personal information the Company collects, how such information is used, stored, protected, and
                            disclosed, and the rights of individuals with respect to their personal information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="1.2">
                        
                            The Company is committed to protecting the privacy and personal information of all participants,
                            students, employees, contractors, visitors, and website users. The Company recognizes the
                            importance of safeguarding personal data and is dedicated to handling all personal information
                            with the utmost care, transparency, and in accordance with applicable law.
                        
                        </x-legal-clause>
                        <x-legal-clause n="1.3">
                        
                            This Statement applies to all personal information collected through enrollment forms, training
                            records, website interactions, electronic and written communications, and in-person activities
                            conducted by or on behalf of the Company.
                        
                        </x-legal-clause>
                        <x-legal-clause n="1.4">
                        
                            This Statement covers all training locations operated by the Company in Robertson County, Davidson
                            County, Sumner County, and Rutherford County, Tennessee, as well as all online and digital
                            interactions with the Company, including but not limited to the Company’s website, email
                            communications, and online enrollment platforms.
                        
                        </x-legal-clause>
                        <x-legal-clause n="1.5">
                        
                            <strong>Cross-Reference to Waiver:</strong> This Privacy Act Statement is intended to be read in
                            conjunction with the Company’s Release of Liability and Assumption of Risk Waiver. Together, these
                            documents form the complete set of agreements governing the Participant’s participation in the
                            Training Program. In the event of any conflict between the provisions of this Privacy Act
                            Statement and the Release of Liability and Assumption of Risk Waiver, the provision that provides
                            greater protection to the Company shall control.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 2: Authority and Legal Basis for Collection</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="2.1">
                        
                            The Company collects personal information pursuant to and in compliance with applicable Tennessee
                            state privacy laws, including but not limited to the Tennessee Information Protection Act (Tenn.
                            Code Ann. § 47-18-3601 et seq.), the Tennessee Consumer Protection Act (Tenn. Code Ann. § 47-18-101
                            et seq.), the Tennessee Identity Theft Deterrence Act, and Tennessee’s data breach notification
                            requirements (Tenn. Code Ann. § 47-18-2107).
                        
                        </x-legal-clause>
                        <x-legal-clause n="2.2">
                        
                            The Company also collects personal information in compliance with applicable federal privacy
                            regulations, including but not limited to the Health Insurance Portability and Accountability Act
                            (HIPAA) where applicable, the Children’s Online Privacy Protection Act (COPPA), the Fair Credit
                            Reporting Act (FCRA) as it pertains to background checks, and the Gramm-Leach-Bliley Act (GLBA) as
                            it pertains to financial information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="2.3">
                        
                            The Company collects personal information pursuant to its legitimate business interests in
                            providing security training services, administering training programs, processing enrollment and
                            payments, ensuring the safety and security of its facilities and participants, and complying with
                            applicable licensing and regulatory requirements.
                        
                        </x-legal-clause>
                        <x-legal-clause n="2.4">
                        
                            The Company collects certain personal information as required for compliance with state licensing
                            and regulatory requirements for security training providers in the State of Tennessee, including
                            information necessary for background checks, certification issuance, and reporting to regulatory
                            agencies.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 3: Information We Collect</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="3.1">
                        
                            <strong>Personal Identification Information:</strong> The Company may collect the following
                            personal identification information: full legal name; date of birth; Social Security Number (if
                            required for licensing or background check purposes); driver’s license number or state-issued
                            identification number; residential and mailing address; telephone number(s); and email
                            address(es).
                        
                        </x-legal-clause>
                        <x-legal-clause n="3.2">
                        
                            <strong>Employment and Background Information:</strong> The Company may collect the following
                            employment and background information: current and prior employment history; military service
                            records (if voluntarily provided by the Participant); criminal background check results (if required
                            for licensing or certification purposes); and professional licenses, certifications, and
                            credentials.
                        
                        </x-legal-clause>
                        <x-legal-clause n="3.3">
                        
                            <strong>Financial Information:</strong> The Company may collect the following financial information:
                            payment card information (credit or debit card numbers, expiration dates, and security codes);
                            billing name and address; payment history and transaction records; and fee and tuition records.
                        
                        </x-legal-clause>
                        <x-legal-clause n="3.4">
                        
                            <strong>Training Records:</strong> The Company may collect the following training-related
                            information: courses enrolled in and completed; attendance records; test and examination scores;
                            certification and licensure status; training evaluations and feedback; and instructor assessments
                            and performance records.
                        
                        </x-legal-clause>
                        <x-legal-clause n="3.5">
                        
                            <strong>Health and Medical Information:</strong> The Company may collect the following health and
                            medical information: medical conditions disclosed by the Participant for safety or accommodation
                            purposes; emergency contact information, including name, relationship, and telephone number; and
                            health insurance information (if voluntarily provided by the Participant for emergency treatment
                            purposes).
                        
                        </x-legal-clause>
                        <x-legal-clause n="3.6">
                        
                            <strong>Digital and Technical Information:</strong> The Company may collect the following digital and
                            technical information through its website and online platforms: Internet Protocol (IP) addresses;
                            browser type and version; device type and operating system; cookies and similar tracking
                            technologies; website usage data, including pages visited and time spent; and login credentials for
                            Company platforms.
                        
                        </x-legal-clause>
                        <x-legal-clause n="3.7">
                        
                            <strong>Communications:</strong> The Company may collect and retain the following communications:
                            emails sent to or received from the Company; telephone call records and voicemail messages; text
                            messages and SMS communications; and written correspondence related to training services,
                            enrollment, or account management.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 4: How We Collect Information</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="4.1">
                        
                            <strong>Directly from the Participant:</strong> The Company collects personal information directly
                            from Participants through enrollment forms, applications, registration documents, written and
                            electronic correspondence, and in-person interactions.
                        
                        </x-legal-clause>
                        <x-legal-clause n="4.2">
                        
                            <strong>Through Website and Online Platforms:</strong> The Company collects information through
                            its website and online platforms, including through cookies, web analytics tools, online enrollment
                            forms, and account registration pages.
                        
                        </x-legal-clause>
                        <x-legal-clause n="4.3">
                        
                            <strong>From Third-Party Sources:</strong> The Company may collect information from third-party
                            sources, including background check providers, state and federal licensing agencies, previous
                            employers (for employment verification purposes), and law enforcement agencies when required by law.
                        
                        </x-legal-clause>
                        <x-legal-clause n="4.4">
                        
                            <strong>During Training Activities:</strong> The Company collects information during training
                            activities, including attendance tracking, assessment and examination records, performance
                            evaluations, and instructor observations.
                        
                        </x-legal-clause>
                        <x-legal-clause n="4.5">
                        
                            <strong>Through Security Systems:</strong> The Company collects information through security
                            systems at its training facilities, including surveillance camera recordings (video and/or audio),
                            facility access logs, and visitor sign-in records.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 5: Purpose and Use of Information</h2>
                    <p class="mb-3 sm:mb-4">
                        The Company uses personal information collected from Participants for the following purposes:
                    </p>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="5.1">
                            Processing enrollment applications and registration for training programs and courses.
                        </x-legal-clause>
                        <x-legal-clause n="5.2">
                            Administering training programs, courses, examinations, and certifications.
                        </x-legal-clause>
                        <x-legal-clause n="5.3">
                            Processing payments, billing, invoicing, and managing financial accounts.
                        </x-legal-clause>
                        <x-legal-clause n="5.4">
                            Complying with state licensing and regulatory requirements for security training providers in the State of Tennessee.
                        </x-legal-clause>
                        <x-legal-clause n="5.5">
                            Conducting required background checks for licensing and certification purposes.
                        </x-legal-clause>
                        <x-legal-clause n="5.6">
                            Communicating training schedules, class changes, safety updates, policy changes, and other operational information.
                        </x-legal-clause>
                        <x-legal-clause n="5.7">
                            Maintaining safety and security at all Company training facilities and premises.
                        </x-legal-clause>
                        <x-legal-clause n="5.8">
                            Improving training programs, curricula, instructional methods, and services offered by the Company.
                        </x-legal-clause>
                        <x-legal-clause n="5.9">
                            Facilitating emergency response and medical treatment authorization in the event of an injury or medical emergency during training activities.
                        </x-legal-clause>
                        <x-legal-clause n="5.10">
                            Ensuring legal compliance, enforcing Company policies, and resolving disputes, claims, and legal proceedings.
                        </x-legal-clause>
                        <x-legal-clause n="5.11">
                            Internal record-keeping, auditing, reporting, and general business operations.
                        </x-legal-clause>
                        <x-legal-clause n="5.12">
                            Marketing and promotional communications (only with the prior express consent of the Participant, and subject to the opt-out provisions described herein).
                        </x-legal-clause>
                    </div>

                    <h2 class="legal-section-heading">Section 6: Sensitive Data — Explicit Consent</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="6.1">
                        
                            Certain categories of personal information collected by the Company constitute “sensitive data,”
                            including but not limited to: Social Security Numbers; government-issued identification numbers;
                            criminal background check results; health and medical information; biometric data; and financial
                            account information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="6.2">
                        
                            The Company shall not process sensitive data without first obtaining the Participant’s explicit,
                            affirmative, opt-in consent. By signing this Privacy Act Statement, the Participant provides such
                            consent for the specific purposes described in this document.
                        
                        </x-legal-clause>
                        <x-legal-clause n="6.3">
                        
                            The Participant may withdraw consent for the processing of sensitive data at any time by submitting
                            a written request to the Company at the address or email provided in Section 18 of this Statement.
                            However, withdrawal of consent may result in the Participant’s inability to continue participation in
                            the Training Program if such data is required for licensing, regulatory compliance, or safety
                            purposes.
                        
                        </x-legal-clause>
                        <x-legal-clause n="6.4">
                        
                            The Company shall process sensitive data only to the extent strictly necessary for the purposes for
                            which consent was obtained and shall apply enhanced security measures to protect such data,
                            including but not limited to encryption, restricted access controls, and secure storage protocols.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 7: Disclosure and Sharing of Information</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="7.1">
                        
                            The Company will not sell, rent, or trade personal information to third parties for marketing
                            purposes. The Company is committed to ensuring that personal information is used only for the
                            purposes described in this Statement.
                        
                        </x-legal-clause>
                        <x-legal-clause n="7.2">
                        
                            The Company may share personal information with the following categories of recipients, solely for
                            the purposes described in this Statement: (a) state licensing and regulatory agencies, as required
                            by law; (b) background check providers, for the purpose of conducting required screenings; (c)
                            payment processors, for the purpose of processing transactions; (d) law enforcement agencies,
                            when required by law, court order, or subpoena; (e) legal counsel retained by the Company; (f)
                            insurance providers, in connection with claims or coverage matters; (g) government agencies with
                            lawful authority to request or require disclosure; and (h) third-party training facilities used by
                            the Company in the delivery of training programs.
                        
                        </x-legal-clause>
                        <x-legal-clause n="7.3">
                        
                            All third-party service providers with whom the Company shares personal information are contractually
                            required to maintain the confidentiality of such information and to use it only for the specific
                            purposes for which it was shared. The Company takes reasonable steps to ensure that third-party
                            service providers maintain appropriate data protection standards.
                        
                        </x-legal-clause>
                        <x-legal-clause n="7.4">
                        
                            The Company may disclose personal information if required by law, regulation, court order,
                            subpoena, or other legal process, or if the Company reasonably believes that disclosure is necessary
                            to protect the rights, property, or safety of the Company, its Participants, or the public.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 8: Opt-Out Rights — Sale of Data and Targeted Advertising</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="8.1">
                        
                            The Company does not sell, trade, or otherwise transfer personal information to third parties for
                            monetary or other valuable consideration.
                        
                        </x-legal-clause>
                        <x-legal-clause n="8.2">
                        
                            The Company does not engage in targeted advertising based on personal information collected from
                            Participants.
                        
                        </x-legal-clause>
                        <x-legal-clause n="8.3">
                        
                            In the event the Company’s practices change in the future, Participants shall be notified in advance
                            and provided with a clear and conspicuous method to opt out of the sale of their personal information
                            or the use of their personal information for targeted advertising, in compliance with the Tennessee
                            Information Protection Act (Tenn. Code Ann. § 47-18-3601 et seq.).
                        
                        </x-legal-clause>
                        <x-legal-clause n="8.4">
                        
                            To exercise opt-out rights or to inquire about the Company’s data sharing practices, Participants
                            may contact the Privacy Officer at the contact information provided in Section 18 of this Statement.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 9: Data Security and Protection</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="9.1">
                        
                            <strong>Physical Security Measures:</strong> The Company maintains physical security measures to
                            protect personal information, including locked filing cabinets, restricted access to areas where
                            records are stored, secure document destruction procedures, and controlled access to Company offices
                            and facilities.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.2">
                        
                            <strong>Electronic Security Measures:</strong> The Company maintains electronic security measures to
                            protect personal information stored in digital format, including data encryption (both in transit and
                            at rest), firewalls, password protection, multi-factor authentication where applicable, and
                            role-based access controls.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.3">
                        
                            <strong>Employee Training:</strong> All Company personnel who handle personal information receive
                            training on proper data handling procedures, privacy policies, and applicable legal requirements.
                            Training is provided at the time of hire and on a periodic basis thereafter.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.4">
                        
                            <strong>Regular Review:</strong> The Company regularly reviews and updates its security practices,
                            policies, and procedures to address evolving threats and vulnerabilities, changes in technology, and
                            updates to applicable laws and regulations.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.5">
                        
                            <strong>Limitation of Guarantee:</strong> While the Company takes reasonable measures to protect
                            personal information, no method of electronic transmission or storage is completely secure. The
                            Company cannot guarantee the absolute security of personal information and shall not be liable for
                            any unauthorized access, disclosure, or loss that occurs despite the implementation of reasonable
                            security measures.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.6">
                        
                            <strong>Breach Notification:</strong> In the event of a data breach affecting personal information,
                            the Company will notify affected individuals and appropriate regulatory authorities in compliance with
                            Tennessee’s breach notification laws (Tenn. Code Ann. § 47-18-2107). Notifications will include a
                            description of the breach, the types of information affected, steps the Company is taking in
                            response, and recommendations for affected individuals to protect themselves.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.7">
                        
                            <strong>PCI-DSS Compliance:</strong> All payment card information collected by the Company is
                            processed and stored in compliance with the Payment Card Industry Data Security Standard (PCI-DSS).
                            The Company utilizes PCI-DSS-compliant payment processors and does not store full credit card
                            numbers, CVV codes, or magnetic stripe data on Company systems.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.8">
                        
                            <strong>NIST Privacy Framework:</strong> The Company maintains a privacy program that substantially
                            conforms to the National Institute of Standards and Technology (NIST) Privacy Framework, as referenced
                            in the Tennessee Information Protection Act (Tenn. Code Ann. § 47-18-3617), to provide an organized
                            approach to identifying and managing privacy risks.
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.9">
                        
                            <strong>Incident Response Timeline:</strong> In the event of a data breach affecting personal
                            information, the Company shall notify affected individuals in the most expedient time possible and
                            without unreasonable delay, but in no event later than forty-five (45) calendar days after
                            discovery of the breach, in compliance with Tennessee’s data breach notification requirements (Tenn.
                            Code Ann. § 47-18-2107).
                        
                        </x-legal-clause>
                        <x-legal-clause n="9.10">
                        
                            <strong>Employee Confidentiality:</strong> All Company employees, contractors, agents, and
                            third-party service providers who have access to personal information are required to execute
                            confidentiality and non-disclosure agreements and are trained on the Company’s privacy and data
                            protection policies. Access to personal information is restricted to authorized personnel on a
                            need-to-know basis.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 10: Data Retention</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="10.1">
                        
                            Training records, including enrollment records, attendance records, test scores, and certification
                            records, shall be retained for a minimum of three (3) years from the date of the Participant’s last
                            training activity, or as otherwise required by state regulations governing security training
                            providers.
                        
                        </x-legal-clause>
                        <x-legal-clause n="10.2">
                        
                            Financial records, including payment records, invoices, and receipts, shall be retained in accordance
                            with Internal Revenue Service (IRS) requirements and applicable State of Tennessee tax regulations.
                        
                        </x-legal-clause>
                        <x-legal-clause n="10.3">
                        
                            Background check records shall be retained only as long as reasonably necessary for the purpose for
                            which they were collected, or as required by applicable law or regulation, whichever period is
                            longer.
                        
                        </x-legal-clause>
                        <x-legal-clause n="10.4">
                        
                            Digital data, website logs, and analytics data shall be retained for a period of six (6) months from
                            the date of collection, unless a longer retention period is required for legal, regulatory, or
                            security purposes.
                        
                        </x-legal-clause>
                        <x-legal-clause n="10.5">
                        
                            Upon expiration of the applicable retention period, personal information shall be securely
                            destroyed, deleted, or de-identified using methods designed to prevent reconstruction or recovery of
                            the information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="10.6">
                        
                            Participants may request information about the specific retention period applicable to their personal
                            data by submitting a written request to the Company at the contact information provided in Section 18
                            of this Statement.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 11: Participant Rights</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="11.1">
                        
                            <strong>Right to Access:</strong> Participants have the right to request and obtain a copy of the
                            personal information held by the Company about them, including the categories of information
                            collected, the purposes for which it is used, and the categories of third parties with whom it has
                            been shared.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.2">
                        
                            <strong>Right to Correction:</strong> Participants have the right to request correction of any personal
                            information held by the Company that is inaccurate, incomplete, or outdated.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.3">
                        
                            <strong>Right to Deletion:</strong> Participants have the right to request deletion of their personal
                            information held by the Company, subject to applicable legal retention requirements, regulatory
                            obligations, and the Company’s legitimate business needs.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.4">
                        
                            <strong>Right to Restrict Processing:</strong> Participants have the right to request that the
                            Company limit how their personal information is used or processed, subject to the Company’s legal and
                            regulatory obligations.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.5">
                        
                            <strong>Right to Opt Out of Marketing:</strong> Participants have the right to opt out of receiving
                            marketing and promotional communications from the Company at any time, without affecting their
                            enrollment or participation in training programs.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.6">
                        
                            <strong>How to Exercise Rights:</strong> To exercise any of the rights described in this Section,
                            Participants must submit a written request to the Company at the address or email provided in
                            Section 18 of this Statement. Requests should include the Participant’s full name, contact
                            information, and a clear description of the right being exercised.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.7">
                        
                            <strong>Response Timeline:</strong> The Company shall respond to all verified privacy rights requests
                            within thirty (30) business days of receipt. If additional time is needed to fulfill the request, the
                            Company shall notify the Participant of the extension and the reason for the delay.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.8">
                        
                            <strong>Identity Verification:</strong> The Company reserves the right to verify the identity of any
                            individual making a privacy rights request before fulfilling such request, to protect against
                            unauthorized access to or disclosure of personal information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.9">
                        
                            <strong>Right to Data Portability:</strong> Participants have the right to obtain a copy of their
                            personal information in a portable, readily usable, and machine-readable format, to the extent
                            technically feasible, to allow transmission to another entity without hindrance.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.10">
                        
                            <strong>Right to Appeal:</strong> If the Company declines to take action on a Participant’s privacy
                            rights request, the Company shall inform the Participant of the denial within forty-five (45)
                            calendar days and provide a written explanation of the reasons for the denial.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.11">
                        
                            <strong>Appeal Process:</strong> The Participant may appeal the Company’s decision by submitting a
                            written appeal to the Privacy Officer within thirty (30) calendar days of receiving the denial. The
                            Company shall respond to the appeal within sixty (60) calendar days. If the appeal is denied, the
                            Company shall provide the Participant with information on how to file a complaint with the Tennessee
                            Attorney General’s office, Division of Consumer Affairs.
                        
                        </x-legal-clause>
                        <x-legal-clause n="11.12">
                        
                            <strong>Non-Discrimination:</strong> The Company shall not discriminate against any Participant for
                            exercising their privacy rights under this Statement. The Company shall not deny services, charge
                            different rates, provide a different level or quality of training, or otherwise penalize a Participant
                            for exercising any rights provided under this Privacy Act Statement or applicable law.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 12: Photographs, Video, and Audio Recordings</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="12.1">
                        
                            The Company may photograph, video record, or audio record training sessions for the purposes of safety
                            documentation, quality assurance, regulatory compliance, instructor evaluation, training
                            improvement, and internal operational review.
                        
                        </x-legal-clause>
                        <x-legal-clause n="12.2">
                        
                            All photographs, video recordings, and audio recordings made by the Company during training sessions
                            are considered internal operational records and are the sole property of the Company.
                        
                        </x-legal-clause>
                        <x-legal-clause n="12.3">
                        
                            The use of photographs, video recordings, or audio recordings for marketing, advertising, social
                            media, promotional materials, or any purpose beyond internal operations requires the separate,
                            explicit, written consent of the Participant through the Company’s Media Release Form. No such use
                            shall occur without the Participant’s prior written authorization.
                        
                        </x-legal-clause>
                        <x-legal-clause n="12.4">
                        
                            Security surveillance cameras may operate at Company training facilities for safety and security
                            purposes. By entering Company premises, Participants consent to being recorded by security
                            surveillance cameras while on Company property. Surveillance footage shall be used solely for safety,
                            security, and incident investigation purposes.
                        
                        </x-legal-clause>
                        <x-legal-clause n="12.5">
                        
                            Participants shall not photograph, video record, or audio record any training sessions, proprietary
                            training materials, course content, or training activities without the prior written consent of the
                            Company. Unauthorized recording is strictly prohibited.
                        
                        </x-legal-clause>
                        <x-legal-clause n="12.6">
                        
                            Any recordings obtained by a Participant in violation of Clause 12.5 may result in the Participant’s
                            immediate removal from the Training Program, forfeiture of tuition and fees, confiscation of recording
                            devices or media, and potential legal action for breach of contract and/or violation of applicable
                            state and federal law.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 13: Website Privacy</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="13.1">
                        
                            <strong>Cookie Policy:</strong> The Company’s website uses cookies and similar tracking technologies
                            to enhance user experience, analyze website traffic, and improve functionality. Cookies used include
                            strictly necessary cookies, performance cookies, and functionality cookies. By using the Company’s
                            website, the user consents to the use of cookies in accordance with this Statement.
                        
                        </x-legal-clause>
                        <x-legal-clause n="13.2">
                        
                            <strong>Third-Party Analytics:</strong> The Company may use third-party analytics services, such as
                            Google Analytics, to collect and analyze information about website usage patterns. These services may
                            collect information such as pages visited, time spent on the website, referring URLs, and general
                            geographic location. Information collected by third-party analytics services is governed by the privacy
                            policies of those providers.
                        
                        </x-legal-clause>
                        <x-legal-clause n="13.3">
                        
                            <strong>Online Enrollment Forms:</strong> Personal information submitted through online enrollment
                            forms, registration pages, and contact forms on the Company’s website is collected and processed in
                            accordance with this Statement. All online forms are transmitted using secure, encrypted connections.
                        
                        </x-legal-clause>
                        <x-legal-clause n="13.4">
                        
                            <strong>Third-Party Links:</strong> The Company’s website may contain links to third-party websites,
                            platforms, or services. The Company is not responsible for the privacy practices, content, or security
                            of any third-party websites. Participants are encouraged to review the privacy policies of any
                            third-party websites they visit.
                        
                        </x-legal-clause>
                        <x-legal-clause n="13.5">
                        
                            <strong>Do Not Track Signals:</strong> The Company’s website does not currently respond to “Do Not
                            Track” signals transmitted by web browsers. However, Participants may manage their cookie preferences
                            through their browser settings.
                        
                        </x-legal-clause>
                        <x-legal-clause n="13.6">
                        
                            <strong>Children’s Privacy:</strong> The Company’s website is not directed at children under the age
                            of thirteen (13). The Company does not knowingly collect personal information from children under
                            thirteen (13) through its website. If the Company becomes aware that it has inadvertently collected
                            personal information from a child under thirteen (13), it will take steps to delete such information
                            promptly.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 14: Communications and Marketing</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="14.1">
                        
                            <strong>Training-Related Communications:</strong> The Company may send training-related communications
                            to enrolled Participants, including class schedules, facility updates, policy changes, safety
                            advisories, and administrative notices. These communications are considered essential to the
                            administration of the Training Program and are non-optional for enrolled Participants.
                        
                        </x-legal-clause>
                        <x-legal-clause n="14.2">
                        
                            <strong>Marketing Communications:</strong> The Company will only send marketing and promotional
                            communications to Participants who have provided their prior express consent to receive such
                            communications. Marketing communications include promotional offers, course advertisements,
                            newsletters, and similar materials.
                        
                        </x-legal-clause>
                        <x-legal-clause n="14.3">
                        
                            <strong>Opt-Out Procedures:</strong> Participants may opt out of receiving marketing and promotional
                            communications at any time by: (a) clicking the “unsubscribe” link included in marketing emails; (b)
                            replying “STOP” to marketing text messages; or (c) submitting a written opt-out request to the
                            Company at the contact information provided in Section 18 of this Statement.
                        
                        </x-legal-clause>
                        <x-legal-clause n="14.4">
                        
                            <strong>Opt-Out Processing:</strong> The Company will honor all opt-out requests within ten (10)
                            business days of receipt. Opting out of marketing communications will not affect the Participant’s
                            receipt of essential training-related communications described in Clause 14.1.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 15: Tennessee-Specific Provisions</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="15.1">
                        
                            <strong>Tennessee Consumer Protection Act:</strong> This Statement is issued in compliance with the
                            Tennessee Consumer Protection Act (Tenn. Code Ann. § 47-18-101 et seq.), which prohibits unfair or
                            deceptive acts or practices in connection with consumer transactions, including the collection and use
                            of personal information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="15.2">
                        
                            <strong>Tennessee Identity Theft Deterrence Act:</strong> The Company complies with the Tennessee
                            Identity Theft Deterrence Act, which requires businesses to take reasonable measures to protect personal
                            identifying information from unauthorized access, use, or disclosure, and to properly dispose of records
                            containing personal identifying information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="15.3">
                        
                            <strong>Data Breach Notification:</strong> The Company complies with Tennessee’s data breach
                            notification requirements (Tenn. Code Ann. § 47-18-2107), which require notification to affected
                            individuals and, in certain circumstances, to the Tennessee Attorney General’s office and consumer
                            reporting agencies, in the event of a breach of personal information.
                        
                        </x-legal-clause>
                        <x-legal-clause n="15.4">
                        
                            <strong>Tennessee Information Protection Act:</strong> The Company complies with the Tennessee
                            Information Protection Act (Tenn. Code Ann. § 47-18-3601 et seq.), which establishes consumer privacy
                            rights, controller obligations, and data protection standards for businesses operating in the State of
                            Tennessee.
                        
                        </x-legal-clause>
                        <x-legal-clause n="15.5">
                        
                            <strong>General Compliance:</strong> The Company is committed to compliance with all applicable
                            Tennessee privacy and data protection laws and regulations. In the event of any change in applicable
                            law, the Company shall update its practices and this Statement accordingly.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 16: Minors and Parental Consent</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="16.1">
                        
                            For Participants under the age of eighteen (18), personal information will only be collected with the
                            informed written consent of a parent or legal guardian. The Company shall not knowingly collect,
                            process, or store personal information from a minor without such consent.
                        
                        </x-legal-clause>
                        <x-legal-clause n="16.2">
                        
                            A parent or legal guardian must review, complete, and sign all enrollment forms, consent forms, and
                            related documents on behalf of the minor Participant. The parent or legal guardian is responsible for
                            ensuring the accuracy of the information provided.
                        
                        </x-legal-clause>
                        <x-legal-clause n="16.3">
                        
                            Parents and legal guardians have the right to: (a) review the personal information the Company has
                            collected from or about their child; (b) request the deletion of their child’s personal information,
                            subject to applicable legal retention requirements; and (c) refuse further collection, use, or
                            disclosure of their child’s personal information. Such requests shall be submitted in writing to the
                            Company at the contact information provided in Section 18 of this Statement.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading">Section 17: Changes to This Privacy Statement</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="17.1">
                        
                            The Company reserves the right to modify, amend, or update this Privacy Act Statement at any time, in
                            its sole discretion, to reflect changes in the Company’s practices, applicable law, or regulatory
                            requirements.
                        
                        </x-legal-clause>
                        <x-legal-clause n="17.2">
                        
                            Changes to this Statement will be effective upon posting of the revised Statement to the Company’s
                            website or upon distribution of the revised Statement to Participants by email, mail, or in-person
                            delivery.
                        
                        </x-legal-clause>
                        <x-legal-clause n="17.3">
                        
                            Continued participation in any Company training program following the posting or distribution of a
                            revised Privacy Act Statement constitutes the Participant’s acceptance of the terms and conditions of
                            the revised Statement.
                        
                        </x-legal-clause>
                        <x-legal-clause n="17.4">
                        
                            The Company will make reasonable efforts to notify Participants of material changes to this Statement,
                            including changes to the types of information collected, the purposes for which information is used, or
                            the categories of third parties with whom information is shared.
                        
                        </x-legal-clause>
</div>

                    <h2 class="legal-section-heading" id="section-18">Section 18: Contact Information</h2>
                    <p class="mb-4">
                        For questions, concerns, or requests regarding this Privacy Act Statement or the Company’s privacy
                        practices, please contact:
                    </p>
                    <div
                        class="rounded-lg border border-gray-200 bg-[#fafafa] p-4 sm:p-6 overflow-x-auto"
                        role="region"
                        aria-label="Company contact details"
                    >
                        <dl class="grid grid-cols-1 gap-y-4 sm:gap-y-3 text-left min-w-0">
                            <div class="sm:grid sm:grid-cols-[minmax(0,11rem)_1fr] sm:gap-x-4 sm:items-start border-b border-gray-200 sm:border-0 pb-3 sm:pb-0 last:border-0 last:pb-0">
                                <dt class="font-bold text-[var(--text-color)] text-sm sm:text-base shrink-0 mb-1 sm:mb-0">Company Name</dt>
                                <dd class="min-w-0 break-words">
                                    Tn Veterans Security Services and Training L.L.C., d/b/a The Security Firm L.L.C.
                                </dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-[minmax(0,11rem)_1fr] sm:gap-x-4 sm:items-start border-b border-gray-200 sm:border-0 pb-3 sm:pb-0">
                                <dt class="font-bold text-[var(--text-color)] text-sm sm:text-base shrink-0 mb-1 sm:mb-0">Mailing Address</dt>
                                <dd class="min-w-0 break-words">4400 Belmont Park Terrace, Suite 100, Nashville, TN 37215</dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-[minmax(0,11rem)_1fr] sm:gap-x-4 sm:items-start border-b border-gray-200 sm:border-0 pb-3 sm:pb-0">
                                <dt class="font-bold text-[var(--text-color)] text-sm sm:text-base shrink-0 mb-1 sm:mb-0">Telephone</dt>
                                <dd class="min-w-0">
                                    <a href="tel:+16155541131" class="whitespace-normal break-all sm:break-normal">(615) 554-1131</a>
                                </dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-[minmax(0,11rem)_1fr] sm:gap-x-4 sm:items-start border-b border-gray-200 sm:border-0 pb-3 sm:pb-0">
                                <dt class="font-bold text-[var(--text-color)] text-sm sm:text-base shrink-0 mb-1 sm:mb-0">Email</dt>
                                <dd class="min-w-0 break-all sm:break-words">
                                    <a href="mailto:{{ 'jayson@TnVeteransSecurity.com' }}">{{ 'jayson@TnVeteransSecurity.com' }}</a>
                                </dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-[minmax(0,11rem)_1fr] sm:gap-x-4 sm:items-start">
                                <dt class="font-bold text-[var(--text-color)] text-sm sm:text-base shrink-0 mb-1 sm:mb-0">Privacy Officer</dt>
                                <dd class="min-w-0 break-words">Jayson Wheat, Owner/CEO</dd>
                            </div>
                        </dl>
                    </div>
                    @if ($siteSettings && ($siteSettings->email || $siteSettings->phone || $siteSettings->address))
                        <p class="text-sm text-[#666] mt-4">
                            General website contact (if different):
                            @if ($siteSettings->email)
                                <a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a>
                            @endif
                            @if ($siteSettings->phone)
                                @if ($siteSettings->email)<span class="mx-1">·</span>@endif
                                <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $siteSettings->phone) }}">{{ $siteSettings->phone }}</a>
                            @endif
                            @if ($siteSettings->address)
                                @if ($siteSettings->email || $siteSettings->phone)<span class="mx-1">·</span>@endif
                                {{ $siteSettings->address }}
                            @endif
                        </p>
                    @endif

                    <h2 class="legal-section-heading">Section 19: Acknowledgment and Consent</h2>
                    <div class="space-y-3 sm:space-y-4">
                        <x-legal-clause n="19.1">
                        
                            By signing below, the Participant acknowledges that they have read, understand, and agree to the terms
                            and conditions of this Privacy Act Statement in its entirety. The Participant has been given the
                            opportunity to ask questions and has received satisfactory answers regarding the Company’s privacy
                            practices.
                        
                        </x-legal-clause>
                        <x-legal-clause n="19.2">
                        
                            The Participant hereby consents to the collection, use, processing, storage, and disclosure of their
                            personal information as described in this Privacy Act Statement, including the collection and processing
                            of sensitive data as described in Section 6.
                        
                        </x-legal-clause>
                        <x-legal-clause n="19.3">
                        
                            The Participant understands and acknowledges their rights regarding their personal information as
                            described in Section 11 of this Statement, including the right to access, correct, delete, restrict
                            processing of, and obtain a portable copy of their personal information, and the right to appeal any
                            denial of a privacy rights request.
                        
                        </x-legal-clause>
                        <x-legal-clause n="19.4">
                        
                            <strong>Cross-Reference:</strong> The Participant acknowledges that this Privacy Act Statement is
                            part of a comprehensive agreement package that includes the Company’s Release of Liability and
                            Assumption of Risk Waiver, and that both documents must be executed as a condition of participation in
                            the Training Program.
                        
                        </x-legal-clause>
</div>

                    <div class="mt-10 sm:mt-12 space-y-6 sm:space-y-8">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-[var(--text-color)] uppercase tracking-wide mb-4">
                                Participant Signature
                            </h3>
                            <div class="space-y-4 rounded-lg border border-gray-300 bg-white p-4 sm:p-5">
                                <div class="grid grid-cols-1 gap-4 sm:gap-5">
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Participant Name (Print)</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Participant Signature</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem] max-w-xs">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Date</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-[var(--text-color)] uppercase tracking-wide mb-2">
                                Parent/Guardian Signature (if participant is under 18)
                            </h3>
                            <p class="text-sm text-[#555] mb-4">
                                Required if the Participant is under the age of eighteen (18) years.
                            </p>
                            <div class="space-y-4 rounded-lg border border-gray-300 bg-white p-4 sm:p-5">
                                <div class="grid grid-cols-1 gap-4 sm:gap-5">
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Parent/Guardian Name (Print)</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem]">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Parent/Guardian Signature</span>
                                    </div>
                                    <div class="border-b border-dashed border-gray-300 pb-2 min-h-[2.5rem] max-w-xs">
                                        <span class="text-xs sm:text-sm font-semibold text-[#666] uppercase">Date</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <footer class="mt-12 sm:mt-16 pt-8 border-t border-gray-200 text-center text-xs sm:text-sm text-[#666] leading-relaxed break-words px-1">
                        <p class="font-medium text-[var(--text-color)]">
                            © Tn Veterans Security Services and Training L.L.C., d/b/a The Security Firm L.L.C. All rights reserved.
                        </p>
                        <p class="mt-2">
                            4400 Belmont Park Terrace, Suite 100, Nashville, TN 37215 · (615) 554-1131 ·
                            <a href="mailto:{{ 'jayson@TnVeteransSecurity.com' }}">{{ 'jayson@TnVeteransSecurity.com' }}</a>
                        </p>
                    </footer>
                </article>
            </div>
        </section>
    </main>
@endsection
