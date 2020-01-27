<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inscription au service</title>
    <style>
        * {
            box-sizing: border-box;
        }

        form {
            width: 30%;
            margin: 0 auto;
        }

        input[type="text"],
        input[type="email"],
        select {
            display: block;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #eee;
            margin: 10px 0;
            width: 100%;
        }

        input.is-invalid,
        select.is-invalid {
            border-color: red;
        }

        label.is-invalid {
            color: red;
        }

        label {
            display: block;
            margin: 10px 0;
        }

        div.errors {
            display: block;
            color: white;
            border-radius: 5px;
            padding: 10px;
            background-color: red;
        }

        button {
            color: white;
            background-color: green;
            border: 1px solid lightgreen;
            border-radius: 5px;
            padding: 10px 20px;
        }
    </style>
</head>

<body>
    <form action="" method="post">
        <h1>Inscription au service</h1>

        <!-- SI LE TABLEAU $errors N'EST PAS VIDE : AFFICHAGE DE CHAQUE ERREUR -->
        <?php if (!empty($errors)) : ?>
            <div class="errors">
                <h2>Des erreurs dans le formulaire : </h2>
                <?php foreach ($errors as $fieldName => $message) : ?>
                    <p>
                        <?= $message ?>
                    </p>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <!-- AFFICHAGE DES CHAMPS AVEC CLASSE SPECIALE SI LE CHAMP EST PRESENT DANS LES ERREURS ET REPRISE DE LA VALEUR SOUMISE SI ELLE EXISTE -->
        <input type="text" name="registration[firstName]" id="firstName" placeholder="Prénom" <?php if (isset($errors['firstName'])) : ?> class="is-invalid" <?php endif ?> value="<?= $data['firstName'] ?? '' ?>">

        <input type="text" name="registration[lastName]" id="lastName" placeholder="Nom de famille" <?php if (isset($errors['lastName'])) : ?> class="is-invalid" <?php endif ?> value="<?= $data['lastName'] ?? '' ?>">

        <input type="email" name="registration[email]" id="email" placeholder="Adresse email" <?php if (isset($errors['email'])) : ?> class="is-invalid" <?php endif ?> value="<?= $data['email'] ?? '' ?>">

        <input type="text" name="registration[phone]" id="phone" placeholder="Numéro de téléphone" <?php if (isset($errors['phone'])) : ?> class="is-invalid" <?php endif ?> value="<?= $data['phone'] ?? '' ?>">

        <select name="registration[position]" id="position" <?php if (isset($errors['position'])) : ?> class="is-invalid" <?php endif ?>>
            <option value="">Choisissez un poste</option>
            <option value="developer" <?php if ($data['position'] && $data['position'] === 'developer') : ?> selected <?php endif ?>>Développeur</option>
            <option value="tester" <?php if ($data['position'] && $data['position'] === 'tester') : ?> selected <?php endif ?>>Testeur</option>
        </select>

        <?php if ($builder->has('agreeTerms')) : ?>
            <label <?php if (isset($errors['agreeTerms'])) : ?> class="is-invalid" <?php endif ?>><input type="checkbox" name="registration[agreeTerms]" id="agreeTerms"> J'accèpte les termes du réglement du service</label>
        <?php endif ?>
        <button type="submit">Je m'inscris</button>
    </form>
</body>

</html>