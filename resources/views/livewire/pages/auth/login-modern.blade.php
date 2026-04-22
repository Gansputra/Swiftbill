<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-split')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="h-screen w-full flex overflow-hidden">
    <style>
        .split-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* Left Side: Form Area */
        .form-section {
            flex: 1;
            background-color: var(--bg-dark);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .logo {
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }

        .logo-text {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            color: white;
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -1px;
            color: white;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 32px;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        input {
            width: 100%;
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 14px 16px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        input:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px var(--glow-color);
            background-color: #222;
        }

        .error-msg {
            color: #ff4d4d;
            font-size: 0.75rem;
            margin-top: 6px;
            display: block;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
        }

        .remember-me:hover {
            color: white;
        }

        .remember-me input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            height: 20px;
            width: 20px;
            background-color: #1a1a1a;
            border: 2px solid #333;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .remember-me:hover .checkmark {
            border-color: var(--accent-primary);
        }

        .remember-me input:checked~.checkmark {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-color: transparent;
            box-shadow: 0 0 10px var(--glow-color);
        }

        .checkmark::after {
            content: "";
            position: absolute;
            display: none;
            width: 6px;
            height: 11px;
            border: solid white;
            border-width: 0 2.5px 2.5px 0;
            transform: rotate(45deg) translate(-1px, -1px);
        }

        .remember-me input:checked~.checkmark::after {
            display: block;
        }

        .forgot-link {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(139, 92, 246, 0.5);
            filter: brightness(1.1);
        }

        .footer-text {
            margin-top: 32px;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .footer-text a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .footer-text a:hover {
            color: var(--accent-secondary);
            text-decoration: underline;
        }

        /* Right Side: Visual Section with Carousel */
        .visual-section {
            flex: 1.25;
            background: var(--bg-darker);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .carousel-view {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            transition: transform 0.6s cubic-bezier(0.65, 0, 0.35, 1);
        }

        .carousel-slide {
            min-width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 80px;
        }

        .visual-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.85;
            z-index: 1;
            transform: scale(1.02);
            transition: transform 15s ease-out;
        }

        .visual-gradient-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top,
                    rgba(10, 10, 10, 1) 5%,
                    rgba(10, 10, 10, 0.6) 40%,
                    rgba(10, 10, 10, 0.1) 100%);
            z-index: 2;
        }

        .visual-content {
            position: relative;
            z-index: 3;
            max-width: 520px;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .visual-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 16px;
            color: white;
            letter-spacing: -1.5px;
            line-height: 1.1;
        }

        .visual-subtitle {
            font-size: 1.2rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 40px;
            font-weight: 300;
        }

        .slider-controls {
            position: absolute;
            bottom: 60px;
            left: 80px;
            z-index: 10;
            display: flex;
            gap: 14px;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .dot.active {
            width: 40px;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.6);
        }

        /* Glows */
        .shape {
            position: absolute;
            z-index: 0;
            filter: blur(120px);
            border-radius: 50%;
            pointer-events: none;
            opacity: 0.5;
        }

        .shape-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--accent-primary), transparent);
            top: -200px;
            right: -200px;
        }

        .shape-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--accent-secondary), transparent);
            bottom: -150px;
            left: -150px;
        }

        @media (max-width: 1100px) {
            .visual-section {
                display: none;
            }
        }
    </style>

    <div class="split-container">
        <!-- Left Side: Form -->
        <div class="form-section">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>

            <div class="form-container">
                <div class="logo">
                    <div class="logo-icon">S</div>
                    <span class="logo-text">Swiftbill</span>
                </div>

                <h1>Welcome back</h1>
                <p class="subtitle">Enter your credentials to access your dashboard.</p>

                <form wire:submit="login">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input wire:model="form.email" type="email" id="email" placeholder="john@example.com" required
                            autofocus>
                        @error('form.email') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input wire:model="form.password" type="password" id="password" placeholder="••••••••" required>
                        @error('form.password') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="remember-forgot">
                        <label class="remember-me">
                            <input wire:model="form.remember" type="checkbox" id="remember">
                            <span class="checkmark"></span>
                            <span>Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="login-btn">
                        <span>Log in</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14m-7-7 7 7-7 7" />
                        </svg>
                    </button>
                </form>

                <p class="footer-text">
                    Don't have an account? <a href="{{ route('register') }}" wire:navigate>Create one here</a>
                </p>
            </div>
        </div>

        <!-- Right Side: Visual Section with Carousel -->
        <div class="visual-section" 
             x-data="{ 
                currentSlide: 0, 
                totalSlides: 3,
                init() {
                    setInterval(() => {
                        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                    }, 6000);
                }
             }">
            <div class="carousel-view" :style="'transform: translateX(-' + (currentSlide * 100) + '%)'">
                <!-- Slide 1 -->
                <div class="carousel-slide">
                    <img src="{{ asset('login_visual_side.png') }}" alt="Dashboard Mockup" class="visual-bg-image">
                    <div class="visual-gradient-overlay"></div>
                    <div class="visual-content">
                        <h2 class="visual-title">Check the status</h2>
                        <p class="visual-subtitle">
                            It's easy to check status of your online orders. Manage inventory, sales, and analytics in
                            one unified dashboard.
                        </p>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-slide">
                    <img src="{{ asset('login_visual_side_2.png') }}" alt="Mobile Mockup" class="visual-bg-image">
                    <div class="visual-gradient-overlay"></div>
                    <div class="visual-content">
                        <h2 class="visual-title">Manage Everywhere</h2>
                        <p class="visual-subtitle">
                            Access your business from any device. Our mobile-first design keeps you in control on the
                            go.
                        </p>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-slide">
                    <img src="{{ asset('login_visual_side_3.png') }}" alt="Analytics" class="visual-bg-image">
                    <div class="visual-gradient-overlay"></div>
                    <div class="visual-content">
                        <h2 class="visual-title">Real-time Insights</h2>
                        <p class="visual-subtitle">
                            Make data-driven decisions with real-time reporting and advanced analytics at your
                            fingertips.
                        </p>
                    </div>
                </div>
            </div>

            <div class="slider-controls">
                <template x-for="i in totalSlides">
                    <button class="dot" 
                            :class="{ 'active': currentSlide === i-1 }"
                            @click="currentSlide = i-1"></button>
                </template>
            </div>
        </div>
    </div>
</div>
</script>
</div>