
<footer class="footer fixed-bottom bg-dark text-light p-2">
        <div class="container">
            <ul class="list-inline">
                @if (Config::get('app.locale') == 'cs')
                <li class="list-inline-item"><bold class="link-light">CS</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'cs')) }}">CS</a></li>
                @endif
                @if (Config::get('app.locale') == 'sk')
                <li class="list-inline-item"><bold class="link-light">SK</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'sk')) }}">SK</a></li>
                @endif
                @if (Config::get('app.locale') == 'hu')
                <li class="list-inline-item"><bold class="link-light">HU</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'hu')) }}">HU</a></li>
                @endif
                @if (Config::get('app.locale') == 'en')
                <li class="list-inline-item"><bold class="link-light">EN</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'en')) }}">EN</a></li>
                @endif
                @include($footer_link)
                <li class="list-inline-item"><a href="">info@slabihoud.cz</a></li>
            </ul>
        </div>
</footer>
</body>
<script src="../../js/bootstrap.js"></script>
<script src="../../js/bootstrap.min.js"></script>
</html>