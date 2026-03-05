@csrf

<div class="mb-3">
    <x-form-input label="Nama Lengkap" name="name" :value="$user->name" />
</div>

<div class="mb-3">
    <label for="email_username" class="form-label">Email</label>
    <div class="input-group">
        <input type="text" id="email_username" class="form-control"
            value="{{ old('email', isset($user->email) ? str_replace('@gmail.com', '', $user->email) : '') }}"
            placeholder="username" required>
        <span class="input-group-text">@gmail.com</span>
    </div>

    {{-- hidden input untuk email utuh --}}
    <input type="hidden" name="email" id="email_hidden">

    @error('email')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <x-form-input label="Password" name="password" type="password" />
</div>

<div class="mb-3">
    <x-form-input label="Konfirmasi Password" name="password_confirmation" type="password" />
</div>

<div class="mt-4">
    <x-primary-button>
        {{ isset($update) ? __('Update') : __('Simpan') }}
    </x-primary-button>

    <x-tombol-kembali :href="route('user.index')" />
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const usernameInput = document.getElementById("email_username");
        const hiddenEmail = document.getElementById("email_hidden");

        function updateEmail() {
            // hanya izinkan huruf, angka, titik, underscore
            usernameInput.value = usernameInput.value.replace(/[^a-zA-Z0-9._]/g, "");
            // update hidden input dengan email valid
            hiddenEmail.value = usernameInput.value + "@gmail.com";
        }

        usernameInput.addEventListener("input", updateEmail);
        updateEmail(); // jalan pertama kali
    });
</script>