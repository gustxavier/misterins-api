@component('mail::message')
<h1 style="text-align: center; margin-bottom:25px">Olá, {{$details['name']}}</h1> 

<p style="margin-bottom: 15px;">Falta pouco para que você redefina sua senha. Clique no botão abaixo para criar uma nova senha.</p>
<p style="text-align: center;"><small><i>Atenção! Não envie este e-mail para outras pessoas. Isso pode colocar a sua conta em risco.</i></small></p>

@component('mail::button', ['url' => $details['link'], 'color' => 'green'])
Redefinir senha
@endcomponent

Atenciosamente,<br>
Equipe {{ config('app.name') }}
@endcomponent
