<a href="/profile">
    <img class="inline-block size-11 rounded-full border-primary" src="{{ Auth::user()->profile_picture
    ? Storage::url(Auth::user()->profile_picture)
    : Storage::url('images/profile-pictures/default-profile.webp') }}" alt="Avatar">
</a>