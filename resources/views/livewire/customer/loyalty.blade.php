<div class="max-w-4xl mx-auto px-4 py-8 space-y-8">

    @if(!$customer || !$program)
        {{-- Empty / unavailable state --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-12 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-surface-container">
                <svg class="h-8 w-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-2">Loyalty Program</h2>
            <p class="text-sm text-secondary max-w-md mx-auto">
                @if(!$program)
                    The loyalty program is not currently active. Check back soon for exciting rewards!
                @else
                    We couldn't find your customer profile. Please make sure you're logged in with the same email you used for your orders.
                @endif
            </p>
        </div>
    @else
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-on-surface">My Loyalty Program</h1>
            <p class="text-sm text-secondary mt-1">{{ $program->name }}</p>
        </div>

        {{-- Points Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Balance --}}
            <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-warning/10">
                    <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <p class="text-3xl font-bold text-on-surface">{{ number_format($stats['balance']) }}</p>
                <p class="text-xs font-medium text-secondary mt-1">Points Balance</p>
            </div>

            {{-- Total Earned --}}
            <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-success/10">
                    <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </div>
                <p class="text-3xl font-bold text-success">{{ number_format($stats['totalEarned']) }}</p>
                <p class="text-xs font-medium text-secondary mt-1">Total Earned</p>
            </div>

            {{-- Total Redeemed --}}
            <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                </div>
                <p class="text-3xl font-bold text-primary">{{ number_format($stats['totalRedeemed']) }}</p>
                <p class="text-xs font-medium text-secondary mt-1">Total Redeemed</p>
            </div>
        </div>

        {{-- Tier Progress (if tiers exist) --}}
        @if($program->tiers && count($program->tiers) > 0)
            <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6">
                <h3 class="text-sm font-bold text-on-surface mb-4">Your Tier</h3>
                <div class="flex items-center gap-4 mb-4">
                    @if($stats['tier'])
                        <span class="rounded-full bg-warning/10 border border-warning/20 px-4 py-1.5 text-sm font-bold text-warning">
                            {{ $stats['tier']['name'] ?? 'Member' }}
                        </span>
                    @else
                        <span class="rounded-full bg-surface-container border border-surface-container-high px-4 py-1.5 text-sm font-bold text-secondary">
                            No Tier Yet
                        </span>
                    @endif

                    @if($stats['nextTier'])
                        <span class="text-xs text-secondary">
                            Next: <span class="font-semibold text-on-surface">{{ $stats['nextTier']['name'] ?? 'Next Level' }}</span>
                            (spend ${{ number_format($stats['nextTier']['min_spent'] ?? 0) }} total)
                        </span>
                    @else
                        <span class="text-xs text-success font-medium">🎉 Highest tier reached!</span>
                    @endif
                </div>

                {{-- Progress bar --}}
                <div class="h-3 w-full rounded-full bg-surface-container overflow-hidden">
                    <div class="h-full rounded-full bg-warning transition-all" style="width: {{ $stats['tierProgress'] }}%"></div>
                </div>

                {{-- Tier ladder --}}
                <div class="mt-4 flex items-center justify-between">
                    @foreach($program->tiers as $t)
                        @php
                            $isActive = $stats['tier'] && ($stats['tier']['name'] ?? '') === ($t['name'] ?? '');
                            $isPast = $stats['tier'] && ($customer->total_spent >= ($t['min_spent'] ?? 0));
                        @endphp
                        <div class="text-center flex-1">
                            <div class="mx-auto mb-1 flex h-8 w-8 items-center justify-center rounded-full {{ $isActive ? 'bg-warning text-white' : ($isPast ? 'bg-success/20 text-success' : 'bg-surface-container text-secondary') }}">
                                @if($isPast)
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                @endif
                            </div>
                            <p class="text-xs font-medium {{ $isActive ? 'text-warning' : 'text-secondary' }}">{{ $t['name'] ?? '' }}</p>
                            <p class="text-[10px] text-secondary">${{ number_format($t['min_spent'] ?? 0) }}+</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- How to Earn Points --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6">
            <h3 class="text-sm font-bold text-on-surface mb-4">How to Earn Points</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Per currency --}}
                <div class="flex items-start gap-3 p-3 rounded-lg bg-surface-container/50">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-success/10">
                        <svg class="h-4.5 w-4.5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-on-surface">{{ $program->points_per_currency }} {{ Str::plural('point', $program->points_per_currency) }} per $1</p>
                        <p class="text-xs text-secondary">Every dollar you spend earns you points</p>
                    </div>
                </div>

                {{-- Per visit --}}
                <div class="flex items-start gap-3 p-3 rounded-lg bg-surface-container/50">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                        <svg class="h-4.5 w-4.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-on-surface">{{ $program->points_per_visit }} {{ Str::plural('point', $program->points_per_visit) }} per visit</p>
                        <p class="text-xs text-secondary">Bonus points just for dining with us</p>
                    </div>
                </div>

                {{-- Birthday --}}
                @if($program->birthday_points > 0)
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-surface-container/50">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-error/10">
                            <svg class="h-4.5 w-4.5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 013 15.546V12a3 3 0 013-3h12a3 3 0 013 3v3.546zM12 4v5"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">{{ $program->birthday_points }} birthday {{ Str::plural('point', $program->birthday_points) }}</p>
                            <p class="text-xs text-secondary">Special bonus on your birthday 🎂</p>
                        </div>
                    </div>
                @endif

                {{-- Review --}}
                @if($program->review_points > 0)
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-surface-container/50">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-warning/10">
                            <svg class="h-4.5 w-4.5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">{{ $program->review_points }} {{ Str::plural('point', $program->review_points) }} per review</p>
                            <p class="text-xs text-secondary">Leave a review and earn points</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Redemption note --}}
            <div class="mt-4 p-3 rounded-lg bg-primary/5 border border-primary/10">
                <p class="text-xs text-secondary">
                    <span class="font-semibold text-primary">Redeem:</span>
                    Minimum {{ number_format($program->minimum_points_redeem) }} points required for redemption.
                    You currently have <span class="font-bold text-on-surface">{{ number_format($stats['balance']) }}</span> points.
                    @if($stats['balance'] >= $program->minimum_points_redeem)
                        <span class="text-success font-semibold">✓ Eligible to redeem!</span>
                    @else
                        <span class="text-secondary">Need {{ number_format($program->minimum_points_redeem - $stats['balance']) }} more points.</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Transaction History --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest">
            <div class="px-6 py-4 border-b border-surface-container-high">
                <h3 class="text-sm font-bold text-on-surface">Transaction History</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container border-b border-surface-container-high">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-secondary text-xs">Date</th>
                            <th class="px-4 py-3 font-semibold text-secondary text-xs">Type</th>
                            <th class="px-4 py-3 font-semibold text-secondary text-xs">Description</th>
                            <th class="px-4 py-3 font-semibold text-secondary text-xs">Order</th>
                            <th class="px-4 py-3 font-semibold text-secondary text-xs text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-high">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-surface-container/50">
                                <td class="px-4 py-3 text-secondary text-xs whitespace-nowrap">{{ $tx->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $typeBadge = match($tx->type) {
                                            'earned' => 'bg-success/10 text-success border-success/20',
                                            'redeemed' => 'bg-primary/10 text-primary border-primary/20',
                                            'bonus' => 'bg-warning/10 text-warning border-warning/20',
                                            'birthday' => 'bg-error/10 text-error border-error/20',
                                            'visit' => 'bg-primary/10 text-primary border-primary/20',
                                            default => 'bg-surface-container text-secondary border-surface-container-high',
                                        };
                                    @endphp
                                    <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $typeBadge }}">
                                        {{ ucfirst($tx->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-on-surface">{{ $tx->description ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-secondary">
                                    @if($tx->order)
                                        #{{ $tx->order->order_number }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold {{ $tx->points >= 0 ? 'text-success' : 'text-error' }}">
                                    {{ $tx->points >= 0 ? '+' : '' }}{{ number_format($tx->points) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-secondary">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-8 w-8 text-secondary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <p class="text-sm">No transactions yet. Start earning points with your next order!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->hasPages())
                <div class="px-4 py-3 border-t border-surface-container-high">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
