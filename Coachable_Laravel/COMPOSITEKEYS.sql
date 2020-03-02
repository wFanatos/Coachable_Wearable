USE Coachable;

ALTER TABLE Parent_Athletes ADD PRIMARY KEY(parent_id, athlete_id);
ALTER TABLE User_Orgs ADD PRIMARY KEY(org_id, user_id);
ALTER TABLE User_Teams ADD PRIMARY KEY(user_id, team_id);
