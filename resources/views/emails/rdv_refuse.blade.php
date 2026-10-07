<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Atelio</h2>
    <p>Bonjour,</p>
    <p>Nous ne pouvons malheureusement pas confirmer votre demande de rendez-vous du <strong>{{ $rendezVous->date_rdv }}</strong>.</p>
    @if($rendezVous->commentaire_admin)
    <p><strong>Motif :</strong> {{ $rendezVous->commentaire_admin }}</p>
    @endif
    <p>N'hésitez pas à soumettre une nouvelle demande à une autre date.</p>
    <p>Merci de votre compréhension.<br>L'équipe Atelio</p>
</body>
</html>
