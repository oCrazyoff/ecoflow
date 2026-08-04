<div id="skeleton-loader" class="fixed inset-0 z-[9999] flex bg-fundo">
    <!-- Sidebar Skeleton (Desktop) -->
    <div class="hidden lg:flex w-64 flex-col bg-white border-r border-borda h-full p-4">
        <div class="h-8 w-32 bg-gray-300 rounded animate-pulse mb-10 mx-auto"></div>
        <div class="flex flex-col gap-4">
            <?php for ($i = 0; $i < 6; $i++): ?>
                <div class="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Main Content Skeleton -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Header -->
        <div class="h-20 w-full bg-white border-b border-borda flex items-center justify-between px-6">
            <div class="h-6 w-40 bg-gray-300 rounded animate-pulse"></div>
            <div class="h-10 w-10 bg-gray-200 rounded-full animate-pulse"></div>
        </div>

        <!-- Dashboard Content -->
        <div class="p-6 flex-1 overflow-y-auto">
            <!-- Header do conteudo -->
            <div class="flex flex-col sm:flex-row justify-between mb-8 gap-4">
                <div class="h-8 w-48 bg-gray-300 rounded animate-pulse"></div>
                <div class="h-10 w-32 bg-gray-200 rounded animate-pulse"></div>
            </div>

            <!-- 4 Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-borda h-32 flex flex-col justify-between">
                        <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                        <div class="h-8 w-32 bg-gray-300 rounded animate-pulse mt-4"></div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Gráfico grande -->
            <div class="bg-white rounded-xl shadow-sm border border-borda p-6 h-80 flex items-center justify-center">
                <div class="w-full h-full bg-gray-100 rounded animate-pulse"></div>
            </div>
        </div>
    </div>
</div>
