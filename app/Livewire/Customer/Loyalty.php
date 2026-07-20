<?php

namespace App\Livewire\Customer;

use App\Models\Tenant\Customer as CustomerModel;
use App\Models\Tenant\LoyaltyProgram;
use App\Models\Tenant\LoyaltyTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class Loyalty extends Component
{
    use WithPagination;

    public function getCustomerProperty()
    {
        return CustomerModel::where('email', auth()->user()->email)->first();
    }

    public function getProgramProperty()
    {
        return LoyaltyProgram::where('is_active', true)->first();
    }

    public function getStatsProperty(): array
    {
        $customer = $this->customer;

        if (! $customer) {
            return [
                'balance' => 0,
                'totalEarned' => 0,
                'totalRedeemed' => 0,
                'tier' => null,
                'nextTier' => null,
                'tierProgress' => 0,
            ];
        }

        $totalEarned = $customer->loyaltyTransactions()
            ->where('points', '>', 0)
            ->sum('points');

        $totalRedeemed = abs(
            $customer->loyaltyTransactions()
                ->where('points', '<', 0)
                ->sum('points')
        );

        // Determine tier
        $tier = null;
        $nextTier = null;
        $tierProgress = 0;
        $program = $this->program;

        if ($program && $program->tiers) {
            $tiers = collect($program->tiers)->sortBy('min_spent');
            $currentSpent = (float) $customer->total_spent;

            foreach ($tiers as $t) {
                if ($currentSpent >= ($t['min_spent'] ?? 0)) {
                    $tier = $t;
                } else {
                    if (! $nextTier) {
                        $nextTier = $t;
                    }
                }
            }

            if ($tier && $nextTier) {
                $tierMin = $tier['min_spent'] ?? 0;
                $nextMin = $nextTier['min_spent'] ?? 0;
                $range = $nextMin - $tierMin;
                $tierProgress = $range > 0
                    ? min(100, round(($currentSpent - $tierMin) / $range * 100))
                    : 100;
            } elseif ($tier && ! $nextTier) {
                $tierProgress = 100;
            }
        }

        return [
            'balance' => $customer->loyalty_points ?? 0,
            'totalEarned' => (int) $totalEarned,
            'totalRedeemed' => (int) $totalRedeemed,
            'tier' => $tier,
            'nextTier' => $nextTier,
            'tierProgress' => $tierProgress,
        ];
    }

    public function render()
    {
        $customer = $this->customer;
        $program = $this->program;
        $stats = $this->stats;
        $transactions = $customer
            ? $customer->loyaltyTransactions()->with('order')->latest()->paginate(15)
            : collect();

        return view('livewire.customer.loyalty', compact(
            'customer',
            'program',
            'stats',
            'transactions'
        ))->layout('layouts.customer');
    }
}
