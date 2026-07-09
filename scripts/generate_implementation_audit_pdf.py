#!/usr/bin/env python3
"""Generate TN Veterans Security Implementation Audit PDF with detailed User Guidelines."""

from fpdf import FPDF
from datetime import date

OUTPUT = r"E:\xampp\htdocs\tn-veterans-security\docs\TN-Veterans-Security-Implementation-Audit.pdf"
BUSINESS_EMAIL = "Jayson@tnveteranssecurity.com"

PAGE_MARGIN = 18
CONTENT_WIDTH = 210 - (PAGE_MARGIN * 2)


class AuditPDF(FPDF):
    def __init__(self):
        super().__init__()
        self.set_margins(PAGE_MARGIN, PAGE_MARGIN, PAGE_MARGIN)
        self.set_auto_page_break(auto=True, margin=20)

    def header(self):
        if self.page_no() > 1:
            self.set_font("Helvetica", "I", 8)
            self.set_text_color(100, 100, 100)
            self.cell(CONTENT_WIDTH / 2, 8, "TN Veterans Security - Documentation & User Guide", align="L")
            self.cell(CONTENT_WIDTH / 2, 8, f"Page {self.page_no()}", align="R", new_x="LMARGIN", new_y="NEXT")
            self.set_draw_color(220, 220, 220)
            self.line(PAGE_MARGIN, self.get_y(), 210 - PAGE_MARGIN, self.get_y())
            self.ln(4)

    def footer(self):
        self.set_y(-15)
        self.set_font("Helvetica", "I", 8)
        self.set_text_color(120, 120, 120)
        self.cell(0, 10, f"{date.today().strftime('%B %d, %Y')} | TN Veterans Security", align="C")

    def _reset_x(self):
        self.set_x(self.l_margin)

    def _write_label(self, label: str):
        self._reset_x()
        self.set_font("Helvetica", "B", 9)
        self.set_text_color(22, 78, 99)
        self.multi_cell(CONTENT_WIDTH, 5, label)
        self.set_text_color(0, 0, 0)

    def _write_body(self, text: str, line_height: float = 5):
        self._reset_x()
        self.set_font("Helvetica", "", 9)
        self.multi_cell(CONTENT_WIDTH, line_height, text)

    def _write_intro(self, text: str, line_height: float = 5.5):
        self._reset_x()
        self.set_font("Helvetica", "", 10)
        self.multi_cell(CONTENT_WIDTH, line_height, text)
        self.ln(2)

    def cover(self):
        self.add_page()
        self.set_fill_color(22, 78, 99)
        self.rect(0, 0, 210, 72, "F")
        self.set_y(20)
        self.set_font("Helvetica", "B", 26)
        self.set_text_color(255, 255, 255)
        self.cell(0, 12, "TN Veterans Security", align="C", new_x="LMARGIN", new_y="NEXT")
        self.set_font("Helvetica", "", 14)
        self.cell(0, 9, "System Documentation & User Guide", align="C", new_x="LMARGIN", new_y="NEXT")
        self.set_font("Helvetica", "", 11)
        self.cell(0, 8, "Features, Verification & Daily Use", align="C", new_x="LMARGIN", new_y="NEXT")

        self.set_y(82)
        self.set_text_color(80, 80, 80)
        self.set_font("Helvetica", "", 10)
        self.cell(0, 7, f"Document Date: {date.today().strftime('%B %d, %Y')}", align="C", new_x="LMARGIN", new_y="NEXT")

        self.ln(10)
        self.set_text_color(0, 0, 0)
        self._reset_x()
        self.set_font("Helvetica", "B", 12)
        self.multi_cell(CONTENT_WIDTH, 7, "About This Document")
        self.ln(2)
        self._write_intro(
            "This document covers the TN Veterans Security website and admin system: "
            "what was built against your specification, and step-by-step instructions "
            "for administrators, students, and public site visitors.",
        )

    def section_title(self, title, level=1):
        if self.get_y() > 265:
            self.add_page()
        self.ln(4)
        self._reset_x()
        if level == 1:
            self.set_font("Helvetica", "B", 14)
            self.set_fill_color(22, 78, 99)
            self.set_text_color(255, 255, 255)
            self.cell(CONTENT_WIDTH, 10, f"  {title}", fill=True, new_x="LMARGIN", new_y="NEXT")
            self.set_text_color(0, 0, 0)
        elif level == 2:
            self.set_font("Helvetica", "B", 12)
            self.set_text_color(22, 78, 99)
            self.multi_cell(CONTENT_WIDTH, 7, title)
            self.set_text_color(0, 0, 0)
        else:
            self.set_font("Helvetica", "B", 10)
            self.multi_cell(CONTENT_WIDTH, 6, title)
        self.ln(3)

    def status_badge(self, status: str):
        colors = {
            "COMPLETE": (34, 139, 34),
            "PARTIAL": (202, 138, 4),
            "NOT COMPLETE": (185, 28, 28),
        }
        c = colors.get(status, (100, 100, 100))
        self._reset_x()
        self.set_fill_color(*c)
        self.set_text_color(255, 255, 255)
        self.set_font("Helvetica", "B", 9)
        self.cell(32, 7, status, fill=True, align="C", new_x="LMARGIN", new_y="NEXT")
        self.set_text_color(0, 0, 0)

    def audit_item(self, req_id, title, status, evidence, notes=""):
        if self.get_y() > 248:
            self.add_page()

        self.ln(2)
        self._reset_x()
        self.set_font("Helvetica", "B", 10)
        self.multi_cell(CONTENT_WIDTH, 6, f"{req_id}  {title}")
        self.ln(1)

        self._write_label("Details:")
        self._write_body(evidence)

        if notes:
            self.ln(1)
            self._write_label("Notes:")
            self._write_body(notes)

        self.ln(4)
        self.set_draw_color(230, 230, 230)
        self.line(PAGE_MARGIN, self.get_y(), 210 - PAGE_MARGIN, self.get_y())
        self.ln(3)

    def bullet(self, text):
        self._reset_x()
        self.set_font("Helvetica", "", 9)
        self.multi_cell(CONTENT_WIDTH, 5, f"- {text}")

    def numbered_step(self, num, text):
        self._reset_x()
        self.set_font("Helvetica", "", 9)
        self.multi_cell(CONTENT_WIDTH, 5, f"{num}. {text}")

    def guideline_block(self, title, steps):
        if self.get_y() > 250:
            self.add_page()
        self._reset_x()
        self.set_font("Helvetica", "B", 10)
        self.multi_cell(CONTENT_WIDTH, 6, title)
        self.ln(1)
        for i, step in enumerate(steps, 1):
            self.numbered_step(i, step)
        self.ln(3)


# All 52 requirement items organized by specification section
AUDIT_ITEMS = [
    # --- 1. HOMEPAGE ---
    ("1.1", "View Security Services Button Links to Affiliates",
     "Hero button uses route('affiliated-services'). Navigates to /affiliated-services.",
     "resources/views/welcome.blade.php"),
    ("1.2", "Homepage Email Uses Business Email",
     f"Email section displays {BUSINESS_EMAIL} with mailto link. Fallback when Site Settings email is empty.",
     "resources/views/welcome.blade.php"),
    ("1.3", "Homepage Text: At TN Veterans",
     "Branding corrected to 'At TN Veterans Security Services and Training' (not '@ Tn Veterans').",
     "resources/views/welcome.blade.php"),

    # --- 2. ABOUT US ---
    ("2.1", "About Us Text: At TN Veterans",
     "About page uses correct 'At TN Veterans Security Services and Training' branding.",
     "resources/views/about.blade.php | /about"),

    # --- 3. CALENDAR ---
    ("3.1", "Calendar Footer Note",
     "Persistent note at bottom: 'If you need a class that is not currently scheduled, please contact us...'",
     "resources/views/class-calendar.blade.php | /class-calendar"),

    # --- 4. AFFILIATES ---
    ("4.1", "Veteran Owned Security Companies (4 links)",
     "Elite Security, Vanguard Security Training, Regiment Security Group, Essential Security Services with correct URLs.",
     "resources/views/affiliated-services.blade.php"),
    ("4.2", "Non Veteran Owned Security Companies (3 links)",
     "SafetyTN Security Solutions, JS Security Consulting, Apex with correct URLs.",
     "resources/views/affiliated-services.blade.php"),
    ("4.3", "Veteran Owned Companies Non Security (3 links)",
     "Guns and Leather, Shooter's Guns Ammo & Range, South Winds Cattle Company.",
     "resources/views/affiliated-services.blade.php"),
    ("4.4", "Non Veteran Owned Companies Non Security (3 links)",
     "Code Blue CPR Services, USLAW Shield, TN Professional Training Institute.",
     "resources/views/affiliated-services.blade.php"),

    # --- 5. TRAINING & CLASSES ---
    ("5.1", "Remove Security Training Classes from Training Tabs",
     "PublicTrainingServiceQuery excludes security_training and renewals categories. "
     "Removed: Renewal Dallas Law, Unarmed/Armed Guard classes, bundles, ASP, Less Than Lethal, etc.",
     "app/Support/PublicTrainingServiceQuery.php | routes/web.php"),
    ("5.2", "Add Handle With Care to Training Tabs",
     "Handle With Care service exists (slug: handle-with-care). Listed in Training menu. NOT under Security Training.",
     "database/seeders/RequirementsComplianceSeeder.php | header.blade.php"),

    # --- 6. SECURITY TRAINING PAGE ---
    ("6.1", "Security Training Hover Menu - 2 Items Only",
     "Menu contains only Initial Registration and Renewal Registration.",
     "resources/views/layouts/web/partials/header.blade.php"),
    ("6.2", "Hospital/School Registration Message",
     "Question 2 of 3: 'Active Shooter and BLS are mandatory. Handle With Care is recommended.'",
     "resources/views/security-training.blade.php"),
    ("6.3", "Remove 3 Simple Steps Section",
     "Section '3 Simple Steps to Security Training' removed from page.",
     "resources/views/security-training.blade.php"),
    ("6.4", "Renewal Section Lists 4 Classes",
     "Renewals page shows: Renewal Dallas Law, Unarmed Guard Renewal, Renewal Enhanced Armed Guard, Armed Guard Renewal.",
     "/renewals | RequirementsComplianceSeeder tags renewals category"),

    # --- 7. CONTACT ---
    ("7.1", "Contact Inquiry Options (All 9)",
     "Dropdown includes: NRA, Handgun Permit, Enhanced Armed Guard, Red Cross, Active Shooter, "
     "Unarmed Guard, Handle With Care, Armed Guard, De escalation.",
     "resources/views/contact.blade.php | contact_submissions table"),

    # --- 8. STUDENT PROFILE ---
    ("8.1", "Security Registration Number Requirement",
     "Question: 'Do you have or have you ever had a Security Registration Number?' "
     "If Yes: Registration Number and Expiration Date required (register, profile, admin edit).",
     "student/auth/register.blade.php | student/profile.blade.php | admin/students/edit.blade.php"),

    # --- 9. DASHBOARD ---
    ("9.1", "Dashboard Customer Count Bug Fixed",
     "Dashboard uses Student::count() and label 'Total Students'. Links to /admin/students. No orphaned customer count.",
     "DashboardController.php | admin/dashboard.blade.php"),
    ("9.2", "Admin Database Access - All 4 Data Types",
     "Admin can access: Customer/Student records, Class registrations (bookings), Contact form submissions, Payment confirmations.",
     "/admin/students | /admin/bookings | /admin/contact-submissions | /admin/payments"),
    ("9.3", "Calendar Email from Business Email",
     "CalendarInviteService uses SiteSetting business email. ICS calendar invite attached to enrollment email.",
     "app/Services/CalendarInviteService.php | Admin Site Settings"),

    # --- 10. CLASS COMMUNICATION ---
    ("10.1", "Notify Enrolled Students Tool",
     "Scheduling screen has 'Notify Enrolled Students' button. Types: canceled, rescheduled, moved, time changed, instructor changed. "
     "Delivery: Email, Text, or Both.",
     "admin/class-schedules/show.blade.php | ClassNotificationController.php"),
    ("10.2", "Notification Logging",
     "ClassNotification logs: timestamp (created_at), message, delivery_method, class_schedule_id, student_ids.",
     "class_notifications table | /admin/communication-logs"),

    # --- 11. ENROLLMENT CONFIRMATION ---
    ("11.1", "Confirmations After Deposit Payment Only",
     "EnrollmentConfirmationService called only after successful deposit payment.",
     "EnrollmentConfirmationService.php | BookingController processPayment"),
    ("11.2", "Student Email - All Required Fields",
     "Email includes: class name, date/time, location, instructor, deposit, remaining balance, what to bring, prerequisites.",
     "resources/views/emails/enrollment-confirmed.blade.php"),
    ("11.3", "Student Text Message Template",
     "SMS: 'You are enrolled in [Class] on [Date] at [Time] at [Location]. Check your email for details.'",
     "EnrollmentConfirmationService.php sendStudentSms()"),
    ("11.4", "Admin Enrollment Alert Email",
     "Includes student name, email, phone, class, deposit amount, payment confirmation ID.",
     "resources/views/emails/admin-enrollment-confirmed.blade.php"),

    # --- 12. PAYMENT ---
    ("12", "Payment & Enrollment Logic",
     "Deposit required before enrollment confirmed. deposit_amount configurable per class. refund_policy field supported.",
     "Service model | admin/classes create/edit | student checkout"),

    # --- 13. CAPACITY & WAITLIST ---
    ("13.1", "Class Capacity Management",
     "max_students field, current_students tracking, prevents over-enrollment, admin override on schedule edit.",
     "ClassSchedule model | BookingController validation"),
    ("13.2", "Waitlist System",
     "Full classes show 'Join Waitlist'. Waitlist entries stored. Admin can notify waitlisted students.",
     "student/available-classes.blade.php | WaitlistController | notify-waitlist form"),

    # --- 14. LOCATION ---
    ("14", "Class Location Management",
     "Location dropdown on schedule creation. Location appears in class listing, calendar, confirmation email, "
     "confirmation SMS, and dashboard.",
     "admin/locations | admin/class-schedules | class-calendar.blade.php"),

    # --- 15. INSTRUCTOR ---
    ("15", "Instructor Assignment",
     "Instructor dropdown on schedule creation. Appears in class listing, confirmation email, dashboard, student portal.",
     "admin/instructors | admin/class-schedules | booking-details views"),

    # --- 16. STUDENT PORTAL ---
    ("16", "Student Portal - All Features",
     "Students can: view upcoming/past classes, download certificates, update contact info, view payment history, "
     "view registration number & expiration, complete online blended modules.",
     "/student/dashboard | /student/bookings | /student/profile | /student/payment-history | /student/certificates | /student/online-courses"),

    # --- 17. ADMIN PORTAL ---
    ("17", "Admin Portal - All Features",
     "Admin can: view/search students, view class rosters, export rosters (CSV), view payment history, "
     "view communication logs, edit/cancel class schedules, add instructors, add locations, edit student profiles.",
     "admin/* routes | StudentController search | BookingController exportRoster"),

    # --- 18. EMAIL & SMS ---
    ("18", "Email & SMS Provider Setup",
     "SMTP via .env MAIL_* settings. Twilio SMS via Admin Site Settings. Error logging in services. Domain verification via DNS (production).",
     "admin/settings | SmsService.php | config/mail.php"),

    # --- 19. MOBILE ---
    ("19", "Mobile Responsiveness",
     "Tailwind responsive layouts on public site, admin panel, and student portal. Tested breakpoints for phone/tablet.",
     "All Blade layouts use responsive Tailwind classes"),

    # --- 20. ACCESSIBILITY ---
    ("20", "Accessibility (WCAG)",
     "Skip-to-content link, aria-labels on social icons, descriptive alt text on images, form labels, keyboard-navigable menus.",
     "layouts/web/master.blade.php | header.blade.php | contact form"),

    # --- 21. PERFORMANCE & SECURITY ---
    ("21", "Performance & Security",
     "HTTPS forced in production (AppServiceProvider). Rate limiting on login (10/min), contact (5/min), booking inquiry. "
     "CSRF protection. Contact form captcha spam protection.",
     "AppServiceProvider.php | routes/web.php throttle middleware | contact captcha"),

    # --- 22. BLENDED COURSES ---
    ("22.1", "Online Course Delivery",
     "Admin creates modules with written content, video, images. Reorder supported. Student access via online courses.",
     "admin/classes/{id}/course-modules | student/online-course"),
    ("22.2", "Module Level Quizzes (90% Pass)",
     "Each module ends with quiz. 90% passing score. Progression locked until pass. Admin configures Q&A.",
     "BlendedCourseService.php PASSING_SCORE = 90"),
    ("22.3", "No Online Final Exam",
     "Only module quizzes online. Final evaluation is in-person hands-on.",
     "BlendedCourseAdminController in-person test"),
    ("22.4", "Completion Tracking",
     "Tracks module completion, quiz scores, attempts, timestamps. Admin can reset or override progression.",
     "student_module_progress table | admin blended-progress"),
    ("22.5", "Automatic Completion Notifications",
     "Email + SMS to student and instructor when online portion complete. Includes name, course, timestamp, eligibility.",
     "BlendedCourseCompletionService.php"),
    ("22.6", "In-Person Testing Eligibility",
     "Student blocked from in-person testing until all modules passed at 90%+.",
     "BlendedCourseService isEligibleForInPersonTesting()"),
    ("22.7", "In-Person Test Results",
     "Admin records Pass, Fail, or Needs Remediation.",
     "admin blended-progress in-person-test form"),
    ("22.8", "No Certificates for Blended Courses",
     "Blended courses do not issue certificates.",
     "CertificateService excludes blended courses"),

    # --- 23. TRAVEL CLASSES ---
    ("23.1", "Travel Classes - Additional Fees",
     "Distance, lodging, travel time fees configurable per class. Admin override supported.",
     "Service travel fields | admin/classes create/edit"),
    ("23.2", "Travel Classes - Minimum Student Requirement",
     "minimum_students field. Dashboard alerts when below minimum. Admin can cancel, reschedule, notify.",
     "TravelClassService.php | admin/dashboard alerts"),
    ("23.3", "Travel Class Scheduling Visibility",
     "Travel classes appear in calendar, class listings, student portal, admin portal.",
     "class-calendar | available-classes | admin class-schedules"),
    ("23.4", "Travel Notes in Communications",
     "Travel notes, location details, lodging instructions in confirmation email, reminder email (ClassReminderMail daily), student portal.",
     "ClassReminderMail | SendClassReminders command | student/booking-details.blade.php"),
]


def build_admin_guidelines(pdf: AuditPDF):
    pdf.add_page()
    pdf.section_title("Section B: Administrator User Guidelines", 1)
    pdf._write_intro(
        f"Admin login URL: /admin/login\nBusiness email: {BUSINESS_EMAIL}\n"
        "Use Chrome or Edge on desktop for best experience. Mobile admin panel is responsive.",
    )

    pdf.section_title("B.1 First-Time Setup", 2)
    pdf.guideline_block("Initial Site Settings (/admin/settings)", [
        "Set business email to Jayson@tnveteranssecurity.com",
        "Enter phone number, address, and social media links",
        "Configure Twilio Account SID, Auth Token, and From Number for SMS",
        "Save QuickBooks credentials if using online payments",
        "Save settings and send a test contact form to verify email delivery",
    ])
    pdf.guideline_block("Add Instructors and Locations", [
        "Go to /admin/instructors > Add Instructor (name, email, phone, bio)",
        "Go to /admin/locations > Add Location (name, address, city, state, zip)",
        "These appear as dropdowns when scheduling classes",
    ])

    pdf.add_page()
    pdf.section_title("B.2 Creating and Managing Training Classes", 2)
    pdf.guideline_block("Add a New Class (/admin/classes > Add New Class)", [
        "Enter class title, description, price, and deposit amount",
        "Set refund policy text (shown to students at checkout)",
        "Choose categories carefully: do NOT add security_training to general classes like Handle With Care",
        "For travel classes: enable travel-based, set minimum students, distance/lodging fees, travel notes",
        "For blended courses: enable blended course flag, then add modules under Course Modules",
        "Upload class image and set prerequisites / what to bring fields (appear in confirmation email)",
    ])
    pdf.guideline_block("Schedule a Class (/admin/class-schedules > Create)", [
        "Select the class from dropdown",
        "Set date, start time, end time",
        "Select instructor and location from dropdowns",
        "Set max_students (capacity). System tracks remaining seats automatically",
        "Status: scheduled (open), full (waitlist only), cancelled, or completed",
        "Save and verify class appears on public calendar and student available-classes page",
    ])
    pdf.guideline_block("Edit or Cancel a Class Schedule", [
        "Open schedule from /admin/class-schedules list",
        "Click Edit to change date, time, instructor, location, or capacity",
        "Change status to 'cancelled' to cancel without deleting",
        "Use 'Notify Enrolled Students' BEFORE or AFTER changes to inform students",
    ])

    pdf.add_page()
    pdf.section_title("B.3 Student and Enrollment Management", 2)
    pdf.guideline_block("View and Search Students (/admin/students)", [
        "Use search box to find by name, email, phone, or registration number",
        "Click student name to view profile, bookings, and registration details",
        "Click Edit to update contact info or security registration number/expiration",
    ])
    pdf.guideline_block("View Class Rosters and Export", [
        "Open any class schedule detail page (/admin/class-schedules/{id})",
        "Enrolled students list shows deposit status and contact info",
        "Click 'Export roster (CSV)' to download spreadsheet for attendance",
    ])
    pdf.guideline_block("Process Bookings and Payments", [
        "View all bookings at /admin/bookings with status and payment filters",
        "View payment confirmations at /admin/payments",
        "Enrollment is confirmed only after deposit_paid status",
    ])
    pdf.guideline_block("Handle Contact Form Submissions", [
        "Review at /admin/contact-submissions",
        "Open each submission to read message and inquiry type",
        "Mark status as read or responded after follow-up",
    ])

    pdf.add_page()
    pdf.section_title("B.4 Class Communications", 2)
    pdf.guideline_block("Notify Enrolled Students (Class Schedule Detail Page)", [
        "Scroll to 'Notify Enrolled Students' section",
        "Select notification type: Class Canceled, Rescheduled, Moved, Time Changed, or Instructor Changed",
        "Select delivery: Email, Text (SMS), or Both",
        "Write clear message with new date/time/location if applicable",
        "Click Send Notification. System logs timestamp, message, method, class ID, and student IDs",
        "Verify delivery in /admin/communication-logs",
    ])
    pdf.guideline_block("Notify Waitlisted Students", [
        "When a seat opens or new schedule added, use 'Notify Waitlist' on schedule detail",
        "Select Email, SMS, or Both and write availability message",
        "Waitlisted students marked as 'notified' after successful delivery",
    ])
    pdf.guideline_block("Automatic Notifications (No Admin Action Required)", [
        "Enrollment confirmation email + SMS sent automatically after deposit payment",
        "Blended course completion email + SMS sent when student finishes all online modules",
        "Class reminder emails sent daily via scheduled command (travel notes included)",
    ])

    pdf.add_page()
    pdf.section_title("B.5 Blended Course Administration", 2)
    pdf.guideline_block("Create Blended Course Modules", [
        "Open class > Course Modules (/admin/classes/{id}/course-modules)",
        "Add module with title, written content, video URL, and images",
        "Add quiz questions with multiple choice answers and correct answer marked",
        "Drag to reorder modules. Students must pass each quiz at 90% before advancing",
    ])
    pdf.guideline_block("Monitor Student Progress", [
        "Go to Blended Progress (/admin/classes/{id}/blended-progress)",
        "View each student's module completion, quiz scores, and attempt counts",
        "Use Override to mark module complete manually if needed",
        "Use Reset to allow student to retake a module",
    ])
    pdf.guideline_block("Record In-Person Test Results", [
        "After student completes all online modules, they become eligible for in-person testing",
        "On blended progress page, select student and record: Pass, Fail, or Needs Remediation",
        "Blended courses do NOT issue certificates",
    ])

    pdf.add_page()
    pdf.section_title("B.6 Travel Class Administration", 2)
    pdf.guideline_block("Configure Travel-Based Classes", [
        "When creating/editing class, enable 'Travel Based' option",
        "Set minimum_students required for class to proceed",
        "Configure distance fee, lodging fee, travel time fee as needed",
        "Add travel notes and lodging instructions (appear in emails and student portal)",
    ])
    pdf.guideline_block("Monitor Travel Class Enrollment", [
        "Dashboard shows alerts when travel class enrollment is below minimum",
        "Admin can cancel, reschedule, or notify enrolled students from schedule page",
        "Travel classes appear on calendar, listings, student portal, and admin portal",
    ])


def build_student_guidelines(pdf: AuditPDF):
    pdf.add_page()
    pdf.section_title("Section C: Student User Guidelines", 1)
    pdf._write_intro("Student login: /student/login | Register: /student/register")

    pdf.section_title("C.1 Account Registration", 2)
    pdf.guideline_block("Create Your Account", [
        "Go to /student/register",
        "Enter full name, email, phone number, and password",
        "Answer: 'Do you have or have you ever had a Security Registration Number?'",
        "If Yes: enter Registration Number and Expiration Date (required fields)",
        "Submit to create account. You can update info later in My Profile",
    ])

    pdf.section_title("C.2 Finding and Enrolling in a Class", 2)
    pdf.guideline_block("Browse Available Classes", [
        "Visit /training-services to browse non-security training classes",
        "Visit /security-training for security guard initial or renewal registration guidance",
        "Visit /class-calendar to see scheduled dates",
        "Click a class to view details, prerequisites, and available schedules",
    ])
    pdf.guideline_block("Complete Enrollment (Deposit Required)", [
        "Select a class schedule (date, time, location, instructor shown)",
        "If class is full, click 'Join Waitlist' instead",
        "Proceed to checkout and pay the required deposit",
        "Enrollment is NOT confirmed until deposit payment succeeds",
        "After payment: check email for full details and phone for confirmation text",
        "Email includes: class, date/time, location, instructor, deposit, balance, what to bring, prerequisites",
    ])

    pdf.section_title("C.3 Student Portal Features", 2)
    pdf.guideline_block("Dashboard (/student/dashboard)", [
        "View upcoming classes with date, time, location, and instructor",
        "View recent past classes",
        "Quick links to bookings, profile, and online courses",
    ])
    pdf.guideline_block("My Bookings (/student/bookings)", [
        "See all enrollments with payment status (pending, deposit paid, fully paid)",
        "Click booking for full details including travel notes if applicable",
        "Complete remaining balance payment if deposit only was paid",
    ])
    pdf.guideline_block("My Profile (/student/profile)", [
        "Update name, email, phone, and address",
        "Update security registration number and expiration date",
        "Upload profile picture",
    ])
    pdf.guideline_block("Payment History (/student/payment-history)", [
        "View all payments with amounts, dates, and transaction IDs",
    ])
    pdf.guideline_block("Online Courses (/student/online-courses)", [
        "Access blended course modules after enrollment and deposit payment",
        "Complete each module's content then take the quiz",
        "Must score 90% or higher to advance to next module",
        "When all modules complete, you receive email and text confirming eligibility for in-person testing",
        "Final testing is hands-on in person. No online final exam. No certificate for blended courses",
    ])
    pdf.guideline_block("Certificates (/student/certificates)", [
        "Download certificates for completed non-blended courses (if applicable)",
        "Blended courses do not issue certificates",
    ])


def build_public_guidelines(pdf: AuditPDF):
    pdf.add_page()
    pdf.section_title("Section D: Public Website Guide", 1)

    pages = [
        ("Homepage (/)", [
            "Overview of TN Veterans Security Services and Training",
            "'View Security Services' button links to Affiliates page",
            "Contact email: Jayson@tnveteranssecurity.com",
            "Branding uses 'At TN Veterans' (not '@ Tn Veterans')",
        ]),
        ("About Us (/about)", [
            "Mission, values, and instructor information",
            "Correct 'At TN Veterans Security Services and Training' branding",
        ]),
        ("Class Calendar (/class-calendar)", [
            "Monthly view of scheduled classes",
            "Footer note: contact us to arrange training at your preferred location if class not listed",
        ]),
        ("Affiliates (/affiliated-services)", [
            "Four sections: Veteran Owned Security, Non Veteran Owned Security,",
            "Veteran Owned Non Security, Non Veteran Owned Non Security",
            "13 partner companies with external links",
        ]),
        ("Training Services (/training-services)", [
            "Browse general training classes by category",
            "Security guard classes and renewals are NOT listed here (by design)",
            "Handle With Care appears here and in Training menu",
        ]),
        ("All Services (/all-services)", [
            "Complete service listing excluding security training and renewals",
        ]),
        ("Security Training (/security-training)", [
            "3-question pre-qualification wizard",
            "Hospital/School answer shows mandatory Active Shooter and BLS, recommended Handle With Care",
            "Links to Initial Registration or Renewal Registration",
        ]),
        ("Initial Registration (/intial-security)", [
            "Security guard initial registration classes and information",
        ]),
        ("Renewals (/renewals)", [
            "Four renewal classes: Renewal Dallas Law, Unarmed Guard Renewal,",
            "Renewal Enhanced Armed Guard, Armed Guard Renewal",
        ]),
        ("Contact Us (/contact-us)", [
            "Contact form with 9 inquiry type options",
            "Math captcha spam protection",
            "Submissions stored for admin review",
        ]),
    ]

    for page_title, steps in pages:
        pdf.guideline_block(page_title, steps)


def build_qa_checklist(pdf: AuditPDF):
    pdf.add_page()
    pdf.section_title("Section E: Feature Checklist", 1)
    pdf._write_intro(
        "Use this list when reviewing the site. Check off each item as you confirm it works.",
    )

    checklist = [
        "1.1  View Security Services button goes to /affiliated-services",
        "1.2  Homepage email shows Jayson@tnveteranssecurity.com",
        "1.3  Homepage says 'At TN Veterans' not '@ Tn Veterans'",
        "2.1  About page says 'At TN Veterans'",
        "3.1  Calendar footer note always visible",
        "4    Affiliates page: 4 sections, 13 companies, correct URLs and order",
        "5.1  Security/renewal classes NOT on /training-services or /all-services",
        "5.2  Handle With Care visible on training tabs and menu",
        "6.1  Security Training menu has only Initial + Renewal Registration",
        "6.2  Hospital/School shows Active Shooter and BLS mandatory message",
        "6.3  '3 Simple Steps' section removed from security training page",
        "6.4  Renewals page shows exactly 4 renewal classes",
        "7.1  Contact form has all 9 inquiry options",
        "8.1  Registration number question with conditional required fields",
        "9.1  Admin dashboard shows Total Students (not broken customer count)",
        "9.2  Admin can access students, bookings, contacts, payments",
        "9.3  Calendar invite uses business email",
        "10.1 Notify Enrolled Students: 5 types, Email/SMS/Both",
        "10.2 Communication logs record all notification details",
        "11.1 Enrollment confirmed only after deposit payment",
        "11.2 Student email has all required enrollment fields",
        "11.3 Student SMS has class, date, time, location",
        "11.4 Admin receives enrollment alert email",
        "12   Deposit required, configurable per class, refund policy works",
        "13.1 Capacity enforced, over-enrollment prevented, admin override works",
        "13.2 Waitlist join, storage, and admin notify works",
        "14   Location dropdown on schedules, visible everywhere required",
        "15   Instructor dropdown on schedules, visible everywhere required",
        "16   Student portal: upcoming/past classes, profile, payments, certificates, online",
        "17   Admin portal: search, rosters, export, logs, edit/cancel, instructors, locations",
        "18   SMTP email and Twilio SMS configurable in admin settings",
        "19   All pages responsive on iPhone, Android, tablet",
        "20   Skip link, alt text, aria labels, keyboard navigation",
        "21   HTTPS in production, rate limiting, captcha spam protection",
        "22.1-22.8 Blended courses: modules, 90% quizzes, tracking, notifications, no certs",
        "23.1-23.4 Travel classes: fees, minimum students, visibility, travel notes in comms",
    ]
    for item in checklist:
        pdf.bullet(f"[ ] {item}")


def build_pdf():
    pdf = AuditPDF()
    pdf.cover()

    # Section A: Point-by-point audit grouped by spec section
    pdf.add_page()
    pdf.section_title("Section A: Specification Features", 1)
    pdf._write_intro(
        "The following sections match your project specification. Each item describes "
        "what was built and where to find it in the system.",
    )

    current_section = None
    section_headers = {
        "1": "1. HOMEPAGE",
        "2": "2. ABOUT US PAGE",
        "3": "3. CALENDAR PAGE",
        "4": "4. AFFILIATES PAGE",
        "5": "5. TRAINING & CLASSES TABS",
        "6": "6. SECURITY TRAINING PAGE",
        "7": "7. CONTACT US PAGE",
        "8": "8. STUDENT PROFILE PAGE",
        "9": "9. DASHBOARD",
        "10": "10. CLASS COMMUNICATION SYSTEM",
        "11": "11. ENROLLMENT CONFIRMATION SYSTEM",
        "12": "12. PAYMENT & ENROLLMENT LOGIC",
        "13": "13. CLASS CAPACITY & WAITLIST",
        "14": "14. CLASS LOCATION MANAGEMENT",
        "15": "15. INSTRUCTOR ASSIGNMENT",
        "16": "16. STUDENT PORTAL",
        "17": "17. ADMIN PORTAL",
        "18": "18. EMAIL & SMS PROVIDER SETUP",
        "19": "19. MOBILE RESPONSIVENESS",
        "20": "20. ACCESSIBILITY",
        "21": "21. PERFORMANCE & SECURITY",
        "22": "22. BLENDED COURSE REQUIREMENTS",
        "23": "23. TRAVEL BASED CLASS REQUIREMENTS",
    }

    for req_id, title, evidence, location in AUDIT_ITEMS:
        section_num = req_id.split(".")[0]
        if section_num != current_section:
            current_section = section_num
            if section_num in section_headers:
                pdf.section_title(section_headers[section_num], 2)
        pdf.audit_item(req_id, title, "COMPLETE", evidence)

    build_admin_guidelines(pdf)
    build_student_guidelines(pdf)
    build_public_guidelines(pdf)
    build_qa_checklist(pdf)

    pdf.ln(5)
    pdf._reset_x()
    pdf.set_font("Helvetica", "I", 9)
    pdf.multi_cell(
        CONTENT_WIDTH, 5,
        f"TN Veterans Security | {BUSINESS_EMAIL}",
    )

    pdf.output(OUTPUT)
    return OUTPUT


if __name__ == "__main__":
    path = build_pdf()
    print(f"PDF generated: {path}")
