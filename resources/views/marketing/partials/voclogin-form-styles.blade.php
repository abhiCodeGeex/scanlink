@push('head')
    <style>
        .mkt__voclogin {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 2.5rem;
            align-items: start;
            max-width: 62rem;
        }
        .mkt__voclogin-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.75rem;
            border: 1px solid #e3e8e3;
            border-radius: 12px;
            background: #fff;
        }
        .mkt__login-status { display: block; color: #2e7d32; font-size: .9rem; margin: 0; }
        .mkt__voclogin-aside h2 { margin-top: 0; font-size: 1.15rem; }
        .mkt__voclogin-aside ul { padding-left: 1.1rem; display: flex; flex-direction: column; gap: .45rem; }
        .mkt__voclogin-note { color: #5d675d; font-size: .92rem; }
        @media (max-width: 860px) {
            .mkt__voclogin { grid-template-columns: minmax(0, 1fr); gap: 1.75rem; }
        }
    </style>
@endpush
