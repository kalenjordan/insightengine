CREATE TABLE insightengine_users (
    user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    is_active TINYINT,
    username VARCHAR(255),
    mandrill_api_key VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    mandrill_last_updated_at DATETIME
);

CREATE TABLE insightengine_tags (
    tag_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(7) NOT NULL,
    is_active TINYINT,
    tag VARCHAR(255),
    tag_subject VARCHAR(255),
    biggest_gap_last_30_days INT,
    send_count INT,
    last_sent DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME
);

ALTER TABLE insightengine_tags ADD UNIQUE INDEX(user_id, tag);
