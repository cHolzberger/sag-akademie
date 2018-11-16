UPDATE kontakt, x_land SET kontakt.land_id = x_land.id WHERE kontakt.land = x_land.name;
UPDATE kontakt, x_bundesland SET kontakt.bundesland_id = x_bundesland.id WHERE kontakt.bundesland = x_bundesland.name;

UPDATE person, x_land SET person.land_id = x_land.id WHERE person.land = x_land.name;
UPDATE person, x_bundesland SET person.bundesland_id = x_bundesland.id WHERE person.bundesland = x_bundesland.name;

UPDATE buchung, x_bundesland SET buchung.bildungscheck_ausstellung_bundesland_id = x_bundesland.id WHERE buchung.bildungscheck_ausstellung_bundesland = x_bundesland.name;
