#!/usr/bin/env python3
"""Generate a 1-page LMS client demo handout PDF."""

from datetime import date

from fpdf import FPDF

OUTPUT = r"E:\xampp\htdocs\tn-veterans-security\docs\TN-Veterans-LMS-Client-Demo-Handout.pdf"


class Handout(FPDF):
    def footer(self):
        self.set_y(-12)
        self.set_font("Helvetica", "I", 8)
        self.set_text_color(110, 110, 110)
        self.cell(
            0,
            5,
            "TN Veterans Security  |  Online Course LMS Demo Handout  |  "
            + date.today().strftime("%b %d, %Y"),
            align="C",
        )


def main() -> None:
    pdf = Handout(format="Letter")
    pdf.set_auto_page_break(auto=False)
    pdf.add_page()
    pdf.set_margins(14, 12, 14)
    pdf.set_y(12)

    pdf.set_fill_color(20, 60, 40)
    pdf.rect(0, 0, 216, 28, "F")
    pdf.set_text_color(255, 255, 255)
    pdf.set_font("Helvetica", "B", 16)
    pdf.set_xy(14, 7)
    pdf.cell(0, 8, "TN Veterans Security", new_x="LMARGIN", new_y="NEXT")
    pdf.set_font("Helvetica", "", 10)
    pdf.set_x(14)
    pdf.cell(
        0,
        6,
        "Online Course LMS - Client Demo Handout (1 page)",
        new_x="LMARGIN",
        new_y="NEXT",
    )

    pdf.set_y(32)
    pdf.set_text_color(30, 30, 30)

    def section(title: str) -> None:
        pdf.set_font("Helvetica", "B", 11)
        pdf.set_text_color(20, 60, 40)
        pdf.cell(0, 6, title, new_x="LMARGIN", new_y="NEXT")
        pdf.set_text_color(35, 35, 35)
        pdf.set_font("Helvetica", "", 9)

    def bullet(text: str) -> None:
        pdf.set_x(16)
        pdf.multi_cell(184, 4.2, "-  " + text)

    section("What this system does")
    bullet("Students complete online modules (video/content + timed quiz) after deposit is paid.")
    bullet("Each module can require a custom passing score and limited attempts (default: 90%, 1 attempt).")
    bullet("Passing all modules unlocks in-person eligibility and issues a course certificate.")
    bullet("Admins monitor progress, review quiz attempts, reset/override modules, and manage certificates.")
    pdf.ln(1.5)

    section("Demo flow (12-15 minutes)")
    steps = [
        (
            "1. Admin setup",
            "Classes > Course Modules: add content, PDF materials, quiz questions, pass % & max attempts.",
        ),
        (
            "2. Deposit unlock",
            "Unpaid booking cannot open online course. Mark Deposit Paid > course unlocks.",
        ),
        (
            "3. Student start",
            "Student > My Online Courses: progress % bar + Continue button.",
        ),
        (
            "4. Module + materials",
            "Open module: lesson content, downloadable PDF, quiz rules shown.",
        ),
        (
            "5. Timed quiz",
            "Start Quiz > countdown timer, one question at a time, no going back.",
        ),
        (
            "6. Pass / fail",
            "Pass unlocks next module. Fail: no free retake; answers hidden until admin reset.",
        ),
        (
            "7. Admin oversight",
            "Class > Blended Progress: Review answers, Override pass, or Reset for re-enroll.",
        ),
        (
            "8. Certificate",
            "All modules passed > student certificate. Admin > Certificates: search, print, revoke.",
        ),
    ]
    for title, body in steps:
        pdf.set_font("Helvetica", "B", 9)
        pdf.set_x(16)
        pdf.cell(36, 4.4, title)
        pdf.set_font("Helvetica", "", 9)
        pdf.multi_cell(148, 4.4, body)
    pdf.ln(1)

    section("Key rules to remember")
    bullet("Online access requires deposit paid or fully paid booking.")
    bullet("Quiz timer is live (server-side). Leaving the page does not pause the clock.")
    bullet("Failed attempt = contact admin to re-enroll/reset after questions are updated.")
    bullet("Certificate issues only when every required online module is completed (or admin override).")
    pdf.ln(1.5)

    section("Login URLs (local demo)")
    pdf.set_x(16)
    pdf.multi_cell(184, 4.2, "Admin:   http://127.0.0.1:8000/admin/login")
    pdf.set_x(16)
    pdf.multi_cell(184, 4.2, "Student: http://127.0.0.1:8000/student/login")
    pdf.ln(1)

    section("Prep checklist before the meeting")
    bullet("Use 2 browser windows (Admin + Student Incognito).")
    bullet("Prepare a blended class with 2 short modules (2-3 quiz questions each).")
    bullet("Upload 1 sample PDF; set clear pass % / attempts on Module 1.")
    bullet("Have one deposit-paid student ready for a fast pass path.")

    pdf.output(OUTPUT)
    print(OUTPUT)


if __name__ == "__main__":
    main()
