@extends('layouts.web.master')

@section('content')
    <main class="overflow-hidden">
        
        <!-- Inner Hero Section -->
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px]">
                    <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        GET <span class="text-[var(--primary-color)]">Security Training</span>
                    </h2>
                    <p class="inner-hero-subtext" data-aos="fade-up" data-aos-delay="200">
                        Launch Your Career with Industry-Leading Security Training and Expert Training.
                    </p>
                </div>
            </div>
        </section>

        {{-- Pre-qualification modal: required before showing training options --}}
        <div id="prequal-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 md:p-8 animate-fade-in">
                <p id="prequal-progress" class="text-sm font-semibold text-[var(--primary-color)] mb-4">Question <span id="prequal-num">1</span> of 3</p>
                <h3 id="prequal-question" class="text-lg md:text-xl font-bold text-[var(--text-color)] mb-6"></h3>
                <div class="flex gap-4">
                    <button type="button" id="prequal-btn-yes" class="flex-1 py-3 px-4 rounded-lg font-semibold bg-[var(--primary-color)] text-white hover:opacity-90 transition">YES</button>
                    <button type="button" id="prequal-btn-no" class="flex-1 py-3 px-4 rounded-lg font-semibold border-2 border-gray-300 text-gray-700 hover:bg-gray-50 transition">NO</button>
                </div>
            </div>
        </div>
        {{-- Sub-popup: required training message (e.g. "Dallas Law is required.") --}}
        <div id="required-msg-overlay" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 text-center">
                <p id="required-msg-text" class="text-lg font-semibold text-[var(--text-color)] mb-6"></p>
                <button type="button" id="required-msg-ok" class="w-full py-3 px-4 rounded-lg font-semibold bg-[var(--primary-color)] text-white hover:opacity-90 transition">OK</button>
            </div>
        </div>

        {{-- Main content: hidden until pre-qualification is complete --}}
        <div id="main-content-after-prequal" class="hidden">
        {{-- Required training highlight (shown after prequal, based on answers) --}}
        <section id="required-training-section" class="py-10 lg:py-14 bg-amber-50/80 border-y border-amber-200/80 hidden">
            <div class="container mx-auto px-4 lg:px-10">
                <h2 class="text-xl md:text-2xl font-bold text-[var(--text-color)] mb-4">Recommended required training for you</h2>
                <div id="required-training-list" class="flex flex-wrap gap-3"></div>
            </div>
        </section>

        <!-- Security Training Overview Section -->
        <section class="py-16 lg:py-24 bg-white">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    
                    <!-- Left Side: Content -->
                    <div class="lg:w-1/2" data-aos="fade-right">
                        <h2 class="text-[30px] md:text-[45px] font-bold text-[var(--text-color)] uppercase mb-6 leading-tight">
                            Your Path with <span class="text-[var(--primary-color)]">Security Training</span>
                        </h2>
                        <p class="text-[16px] md:text-[20px] text-[#333] leading-relaxed mb-6">
                            Our security training programs give you the knowledge, skills, and legal understanding you need for a stable career in the industry. From armed and unarmed guard training to specialized courses, we cover what matters on the job.
                        </p>
                        <p class="text-[16px] md:text-[20px] text-[#333] leading-relaxed mb-8">
                            We offer a clear path from training to certification—efficient, state-approved, and led by veteran instructors so you can get job-ready without cutting corners.
                        </p>
                        
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-[var(--primary-color)] flex items-center justify-center text-white shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-[18px] font-medium text-[var(--text-color)]">State-Approved Curriculum</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-[var(--primary-color)] flex items-center justify-center text-white shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-[18px] font-medium text-[var(--text-color)]">Expert Veteran Instructors</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-[var(--primary-color)] flex items-center justify-center text-white shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-[18px] font-medium text-[var(--text-color)]">Hands-on Practical Training</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Image/Card bundle -->
                    <div class="lg:w-1/2" data-aos="fade-left">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <img src="{{ asset('images/training-img-1.png') }}" alt="Security training classroom session" class="w-full h-[200px] object-cover rounded-sm">
                                <img src="{{ asset('images/training-img-2.png') }}" alt="Instructor leading security training" class="w-full h-[280px] object-cover rounded-sm">
                            </div>
                            <div class="space-y-4">
                                <img src="{{ asset('images/training-img-3.png') }}" alt="Hands-on security skills practice" class="w-full h-[280px] object-cover rounded-sm">
                                <img src="{{ asset('images/training-img-4.png') }}" alt="Security guard certification training" class="w-full h-[200px] object-cover rounded-sm">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Security Training Overview Section -->
        <section class="ready-section">
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="text-left md:text-center lg:text-left md:mx-auto lg:mx-0">
                    <h2 class="mb-5" data-aos="fade-up">
                        <span class="block text-[18px] md:text-[24px] text-white font-normal">Ready to start</span>
                        <span class="block text-[30px] md:text-[45px] font-black leading-tight uppercase">
                            <span class="text-[#F6CB42]">SECURITY TRAINING</span> <span class="text-[#FFFFFF]">?</span>
                        </span>
                    </h2>
                    <p class="text-[16px] md:text-[20px] text-white font-normal mb-8 md:mx-auto lg:mx-0" data-aos="fade-up" data-aos-delay="200">
                        Enroll in our next security training session and take the first step towards a rewarding career.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-start md:justify-center lg:justify-start" data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ route('intial-security') }}" class="btn primary-button !text-center">Enroll Now</a>
                        <a href="{{ route('contact') }}" class="btn secondary-button !text-center">Contact Us</a>
                    </div>
                </div>
            </div>
        </section>

        </div>{{-- end #main-content-after-prequal --}}

    </main>

    <script>
    (function() {
        var questions = [
            { q: 'Will you be working around Alcohol?', yesMsg: 'Dallas Law is required.', key: 'Dallas Law' },
            { q: 'Will you be working at a school or hospital?', yesMsg: 'Active Shooter and BLS are mandatory. Handle With Care is recommended.', key: 'Active Shooter, BLS, Handle With Care' },
            { q: 'Do you plan to carry Mace, Baton, Taser, or Handcuffs?', yesMsg: 'Less Than Lethal training is required.', key: 'Less Than Lethal' }
        ];
        var step = 0;
        var requiredTrainings = [];
        var modal = document.getElementById('prequal-modal');
        var mainContent = document.getElementById('main-content-after-prequal');
        var progressNum = document.getElementById('prequal-num');
        var questionEl = document.getElementById('prequal-question');
        var btnYes = document.getElementById('prequal-btn-yes');
        var btnNo = document.getElementById('prequal-btn-no');
        var overlay = document.getElementById('required-msg-overlay');
        var msgText = document.getElementById('required-msg-text');
        var msgOk = document.getElementById('required-msg-ok');
        var requiredSection = document.getElementById('required-training-section');
        var requiredList = document.getElementById('required-training-list');

        function showQuestion() {
            if (step >= questions.length) {
                modal.classList.add('hidden');
                mainContent.classList.remove('hidden');
                if (requiredTrainings.length) {
                    requiredSection.classList.remove('hidden');
                    requiredList.innerHTML = requiredTrainings.map(function(t) {
                        return '<span class="inline-block px-4 py-2 rounded-lg bg-amber-200/90 text-[var(--text-color)] font-semibold">' + t + '</span>';
                    }).join('');
                }
                return;
            }
            progressNum.textContent = step + 1;
            questionEl.textContent = questions[step].q;
        }

        function nextQuestion() {
            step++;
            showQuestion();
        }

        btnYes.addEventListener('click', function() {
            var cur = questions[step];
            requiredTrainings.push(cur.key);
            msgText.textContent = cur.yesMsg;
            overlay.classList.remove('hidden');
        });

        btnNo.addEventListener('click', function() {
            nextQuestion();
        });

        msgOk.addEventListener('click', function() {
            overlay.classList.add('hidden');
            nextQuestion();
        });

        showQuestion();
    })();
    </script>
@endsection
