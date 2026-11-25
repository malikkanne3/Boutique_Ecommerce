@props(['role'])
@php $key = in_array($role, ['admin', 'vendeur', 'livreur'], true) ? $role : 'client'; @endphp
<span class="pill pill-role-{{ $key }}">{{ \App\Models\User::ROLES[$key] }}</span>
