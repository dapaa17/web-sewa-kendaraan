<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } else {
            return $this->customerDashboard();
        }
    }

    public function adminDashboard()
    {
        $vehicles = Vehicle::all();

        $totalVehicles = $vehicles->count();
        $availableVehicles = $vehicles->filter(fn (Vehicle $vehicle) => $vehicle->current_rental_status === 'available')->count();
        $rentedVehicles = $vehicles->filter(fn (Vehicle $vehicle) => $vehicle->current_rental_status === 'rented')->count();
        $maintenanceVehicles = $vehicles->filter(fn (Vehicle $vehicle) => $vehicle->current_rental_status === 'maintenance')->count();

        $totalBookings = Booking::count();
        $pendingBookings = Booking::displayPending()->count();
        $waitingListBookings = Booking::where('status', 'waiting_list')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();

        $totalRevenue = Booking::where('status', 'completed')->sum('total_price');

        $recentBookings = Booking::with('user', 'vehicle')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalVehicles',
            'availableVehicles',
            'rentedVehicles',
            'maintenanceVehicles',
            'totalBookings',
            'pendingBookings',
            'waitingListBookings',
            'confirmedBookings',
            'totalRevenue',
            'recentBookings'
        ));
    }

    private function customerDashboard()
    {
        $user = Auth::user();

        $totalBookings = $user->bookings()->count();
        $pendingBookings = $user->bookings()->displayPending()->count();
        $waitingListBookings = $user->bookings()->where('status', 'waiting_list')->count();
        $confirmedBookings = $user->bookings()->where('status', 'confirmed')->count();
        $completedBookings = $user->bookings()->where('status', 'completed')->count();

        $upcomingBookings = $user->bookings()
            ->where('status', 'confirmed')
            ->where('start_date', '>=', now())
            ->limit(5)
            ->get();

        $recentBookings = $user->bookings()
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.customer', compact(
            'totalBookings',
            'pendingBookings',
            'waitingListBookings',
            'confirmedBookings',
            'completedBookings',
            'upcomingBookings',
            'recentBookings'
        ));
    }
}