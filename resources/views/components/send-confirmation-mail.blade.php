<form method="POST" action="{{ route('send.confirmation.mail') }}" class="mt-4">
    @csrf
    <input type="hidden" name="subdomain" value="{{ session('subdomain') }}">
    <input type="hidden" name="email" value="{{ session('email') }}">
    <input type="hidden" name="association_name" value="{{ session('association_name') }}">
    <button type="submit" class="w-full bg-accent text-white font-bold py-3 rounded-xl hover:bg-accent/90 transition text-lg text-center">M'envoyer le mail de confirmation</button>
</form>
