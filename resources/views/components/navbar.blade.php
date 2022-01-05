<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Home</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link  @if (URL::current() == route('dashboard')) active @endif" href="{{route('dashboard')}}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if (URL::current() == route('discussion')) active @endif" href="{{route('discussion')}}">Discussions
                        ({{Auth::user()->unreadNotifications->count()}})
                    </a>
                    @if (request()->username)
                        @if(url()->current() == route('discussions',['username'=>request()->username]))
                            @foreach (Auth::user()->unreadNotifications as $notifs)
                                @php
                                    $notifs->update(['read_at'=>now()]);
                                @endphp
                            @endforeach
                        @endif
                    @endif
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('logout')}}">Log Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

