<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center justify-center w-full bg-teal-600 text-white py-2 px-5 rounded-xl']) }}>
    {{ $slot }}
</button>
