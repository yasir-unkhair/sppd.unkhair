<div>
    @if ($flash = flashAllert())
        {!! $flash !!}
    @else
        <p class="login-box-msg">Masuk untuk memulai sesi Anda</p>
    @endif

    <form wire:submit="checklogin">
        <div class="input-group">
            <input type="text" class="form-control" wire:model="username" placeholder="Masukkan identitas anda">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
        @error('username')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="input-group mt-3">
            <input type="password" class="form-control" wire:model="password" placeholder="Password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="input-group mt-3">
            <select wire:model="tahun" class="form-control">
                <option value="">-- Pilih Tahun --</option>
                @for ($i = date('Y'); $i >= 2024; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-calendar"></span>
                </div>
            </div>
        </div>
        @error('tahun')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="row mt-3">
            <div class="col-7">
                <a href="#">Saya lupa kata sandi</a>
            </div>
            <!-- /.col -->
            <div class="col-5">
                <button type="submit" class="btn btn-primary btn-block" wire:loading.attr="disabled"
                    wire:target="checklogin">
                    <span wire:loading.remove wire.target="checklogin">Sign In</span>
                    <span wire:loading wire.target="checklogin"><span class="spinner-border spinner-border-sm"
                            role="status" aria-hidden="true"></span> Waiting...</span>
                </button>
            </div>
            <!-- /.col -->
        </div>
    </form>
    <!-- /.social-auth-links -->
</div>
