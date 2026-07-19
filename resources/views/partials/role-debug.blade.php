@auth('tenant')
<div class="fixed bottom-0 right-0 z-50 bg-yellow-100 border border-yellow-400 text-yellow-800 text-xs px-3 py-2 rounded-tl shadow-lg">
    <div><strong>{{ auth('tenant')->user()?->name ?? 'N/A' }}</strong> ({{ auth('tenant')->user()?->email ?? 'N/A' }})</div>
    <div>
        <strong>Roles:</strong>
        @foreach(auth('tenant')->user()?->getRoleNames() ?? [] as $role)
            <span class="inline-block bg-yellow-200 px-1 rounded">{{ $role }}</span>
        @endforeach
    </div>
    <div>
        <strong>All Permissions:</strong>
        @foreach(auth('tenant')->user()?->getAllPermissions() ?? [] as $perm)
            <span class="inline-block bg-yellow-200 px-1 rounded">{{ $perm->name }}</span>
        @endforeach
    </div>
</div>
@endauth
