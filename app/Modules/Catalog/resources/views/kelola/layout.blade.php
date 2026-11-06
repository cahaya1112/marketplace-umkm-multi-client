{{-- Perantara ke layouts.dasbor (butuh $brandSub dan $menu). Menu sama dengan UmkmDashboardController. --}}
@php
    $umkmSaya = auth()->user()?->umkm;
    $brandSub = $umkmSaya?->nama ?? 'Admin UMKM';
    $idUmkm   = $umkmSaya?->id_umkm ?? 0;

    $menu = [
        ['label' => 'Dashboard', 'icon' => 'grid', 'href' => route('umkm.dashboard', ['umkm' => $idUmkm]), 'active' => false],
        ['label' => 'UMKM Saya', 'icon' => 'store', 'href' => route('kelola.toko.edit'), 'active' => request()->routeIs('kelola.toko.*')],
        ['label' => 'Produk', 'icon' => 'box', 'href' => route('kelola.produk.index'), 'active' => request()->routeIs('kelola.produk.*')],
        ['label' => 'Stok', 'icon' => 'layers', 'href' => route('kelola.stok.index'), 'active' => request()->routeIs('kelola.stok.*')],
        ['label' => 'Pesanan', 'icon' => 'cart', 'href' => '#'],
        ['label' => 'Permintaan Custom', 'icon' => 'filepen', 'href' => route('kelola.custom.index'), 'active' => request()->routeIs('kelola.custom.*')],
        ['label' => 'Metode Pengiriman', 'icon' => 'truck', 'href' => '#'],
        ['label' => 'Retur', 'icon' => 'undo', 'href' => '#'],
        ['label' => 'Pengembalian Dana', 'icon' => 'dollar', 'href' => '#'],
        ['label' => 'Notifikasi', 'icon' => 'bell', 'href' => '#'],
        ['label' => 'Profil', 'icon' => 'user', 'href' => '#'],
    ];
@endphp
@extends('layouts.dasbor')