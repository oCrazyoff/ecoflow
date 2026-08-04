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

        <!-- Perfil Content -->
        <div class="p-6 flex-1 overflow-y-auto">
            <!-- Header do conteudo -->
            <div class="flex flex-col sm:flex-row justify-between mb-8 gap-4">
                <div class="h-8 w-48 bg-gray-300 rounded animate-pulse"></div>
            </div>

            <!-- Formulários Perfil -->
            <div class="bg-white rounded-xl shadow-sm border border-borda p-6 max-w-2xl">
                <div class="flex flex-col items-center mb-8">
                    <div class="h-24 w-24 bg-gray-300 rounded-full animate-pulse mb-4"></div>
                    <div class="h-6 w-40 bg-gray-200 rounded animate-pulse"></div>
                </div>

                <div class="flex flex-col gap-6">
                    <div class="h-12 w-full bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-12 w-full bg-gray-100 rounded animate-pulse"></div>
                    <div class="h-12 w-32 bg-gray-300 rounded animate-pulse mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
