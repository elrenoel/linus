<main class="flex h-full items-center justify-center px-4 py-8">
    <section
        class="flex h-fit w-125 flex-col items-center gap-3.75 rounded-[10px] border-[0.5px] border-[#6E6E6E] bg-[#FEFEFE] px-5 py-10">
        <div class="flex w-full flex-col items-center gap-5">
            <div class="flex w-full items-center justify-center gap-1.25">
                <img
                    src="<?= app_url('/assets/logo.png') ?>"
                    alt="Linus logo"
                    class="h-15 w-15" />
                <span class="text-[38.832px] font-bold leading-8.5 tracking-[-0.03em] text-black">
                    LINUS
                </span>
            </div>
            <div class="flex w-full flex-col items-center gap-1.25">
                <h1 class="text-center text-[24px] font-semibold leading-8.5 tracking-[-0.04em] text-black">
                    Create your account
                </h1>
                <p class="text-center text-[15px] font-medium leading-8.5 tracking-[-0.04em] text-[#545454]">
                    please fill out all the required fields
                </p>
            </div>
        </div>

        <form class="flex w-full flex-col gap-3.75" action="<?= app_url('/logic/auth_register.php') ?>" method="post">
            <div class="flex w-full flex-col gap-1.25">
                <label
                    for="username"
                    class="text-[15px] font-medium leading-4.25 tracking-[-0.04em] text-[#6E6E6E]">
                    Username
                </label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    placeholder="username"
                    class="h-13.5 w-full rounded-lg border-[0.5px] border-[#6E6E6E] px-2.5 text-[15px] font-medium leading-8.5 tracking-[-0.04em] text-black outline-none" />
            </div>

            <div class="flex w-full flex-col gap-1.25">
                <label
                    for="email"
                    class="text-[15px] font-medium leading-4.25 tracking-[-0.04em] text-[#6E6E6E]">
                    Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="test@mail.com"
                    class="h-13.5 w-full rounded-lg border-[0.5px] border-[#6E6E6E] px-2.5 text-[15px] font-medium leading-8.5 tracking-[-0.04em] text-black outline-none" />
            </div>

            <div class="flex w-full flex-col gap-1.25">
                <label
                    for="password"
                    class="text-[15px] font-medium leading-4.25 tracking-[-0.04em] text-[#6E6E6E]">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="password"
                    class="h-13.5 w-full rounded-lg border-[0.5px] border-[#6E6E6E] px-2.5 text-[15px] font-medium leading-8.5 tracking-[-0.04em] text-black outline-none" />
            </div>

            <button
                type="submit"
                class="flex h-13.5 w-full items-center justify-center rounded-lg bg-[#427435] px-2.5 text-[15px] font-medium leading-8.5 tracking-[-0.04em] text-white">
                Sign up
            </button>

            <p class="text-center text-[15px] font-medium leading-4.25 tracking-[-0.04em] text-[#6E6E6E]">
                already have an account ?
                <a href="<?= app_url('/login') ?>" class="text-[#4C45D1]">Sign in</a>
            </p>
        </form>
    </section>
</main>