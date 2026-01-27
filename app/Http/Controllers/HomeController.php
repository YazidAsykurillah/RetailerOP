<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Transaction;
use App\Models\User;
use App\Models\StockMovement;
use App\Models\Purchase;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Product Statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::count();
        $totalBrands = Brand::count();

        // Stock Statistics
        $totalVariants = ProductVariant::count();
        $lowStockCount = ProductVariant::lowStock()->count();
        $outOfStockCount = ProductVariant::where('stock', 0)->count();
        $totalStockValue = ProductVariant::selectRaw('SUM(stock * price) as total')->value('total') ?? 0;

        // Transaction Statistics
        $totalTransactions = Transaction::count();
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        $totalRevenue = Transaction::sum('grand_total');
        $todayRevenue = Transaction::whereDate('created_at', today())
            ->sum('grand_total');

        // User Statistics
        $totalUsers = User::count();

        // Recent Stock Movements
        $recentStockIn = StockMovement::stockIn()
            ->latest()
            ->take(5)
            ->sum('quantity');
        
        $recentStockOut = StockMovement::stockOut()
            ->latest()
            ->take(5)
            ->sum('quantity');

        // Purchase Statistics
        $totalPurchases = Purchase::count();
        $pendingPurchases = Purchase::where('status', 'pending')->count();
        $completedPurchases = Purchase::where('status', 'completed')->count();
        $totalPurchaseCost = Purchase::sum('total_amount');

        return view('home', compact(
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'totalBrands',
            'totalVariants',
            'lowStockCount',
            'outOfStockCount',
            'totalStockValue',
            'totalTransactions',
            'todayTransactions',
            'totalRevenue',
            'todayRevenue',
            'totalUsers',
            'recentStockIn',
            'recentStockOut',
            'totalPurchases',
            'pendingPurchases',
            'completedPurchases',
            'totalPurchaseCost'
        ));
    }
}
