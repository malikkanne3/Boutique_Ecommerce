<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="font-bold text-indigo-600 text-lg">🛒 Boutique</a>
                </div>
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600">Accueil</a>
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600">Boutique</a>
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600">Panier</a>
                    @auth
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600">Mes commandes</a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-red-600 hover:text-red-800">Admin</a>
                    @endif
                    @endauth
                </div>
            </div>
            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                @auth
                    <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-indigo-600">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-red-500 hover:underline">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-indigo-600">Connexion</a>
                    <a href="{{ route('register') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">S'inscrire</a>
                @endauth
            </div>
        </div>
    </div>
</nav>