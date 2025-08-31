@component('mail::message')
# 🔐 Verificación de seguridad

Hola **{{ $user->name }}**  

Tu código de verificación es:

@component('mail::panel')
<table role="presentation" width="100%">
    <tr>
        <td style="text-align: center;">
            <span style="font-size: 30px; font-weight: bold;">{{ $codigo }}</span>
        </td>
    </tr>
</table>
@endcomponent

⚠️ Este código expira en **{{ $ttl }} segundos**.  

Gracias por tu confianza,<br>
El equipo de Soporte
@endcomponent
